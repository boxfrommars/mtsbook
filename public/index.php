<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

$startTime = microtime(true);
$loader = require_once __DIR__ . '/../vendor/autoload.php';
/** @var \Book\BookApplication|\Doctrine\Common\Cache\Cache[]|\Symfony\Component\Form\FormFactory[]|Twig_Environment[]|\Doctrine\DBAL\Connection[]|\Book\BookService[]|\Symfony\Component\HttpFoundation\Session\Session[] $app */
$app = new \Book\BookApplication(array(
    'starttime' => $startTime,
    'debug' => true,
    'tmp_path' => '../tmp',
    'files_path' => realpath(__DIR__ . '/../public/files'),
    'is_cache' => false,
    'application_path' => realpath(__DIR__ . '/..'),
    'config' => array(
        'db' => array(
            'db.options' => array(
                'driver' => 'pdo_pgsql',
                'host' => 'localhost',
                'dbname' => 'mtsbook',
                'user' => 'mtsbook',
                'password' => 'mtsbook',
            ),
        ),
    ),
));

$app->register(new \Book\BookServiceProvider(), array());

$app->get('/', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    return $app['twig']->render('layout.twig', array(
        'content' => $app['twig']->render('books.twig', array('books' => $app['book.service']->fetchAll())),
    ));
});

$app->get('/book/{id}', function ($id) use ($app) {
    return $app['twig']->render('layout.twig', array(
        'content' => $app['twig']->render('book.twig', array('book' => $app['book.service']->fetch($id))),
    ));
});

$app->get('/download/{id}/{format}', function ($id, $format) use ($app) {
    /** @var \Book\BookEntity $book */
    $book = $app['book.service']->fetch($id);
    if (!$book) throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
    $filename = ($format == 'fb2') ? $book->getFileFb2() : $book->getFileEpub();

    $info = array_merge(parse_user_agent(), array('id_book' => $book->getId(), 'format' => $format));

    $app['db']->insert('download', $info);

    return $app->sendFile($app['files_path'] . '/' . $filename)->setContentDisposition(
        \Symfony\Component\HttpFoundation\ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        URLify::filter($book->getAuthor() . '_' . $book->getTitle(), 60, "", true) . '.' . $format
    );
})->assert('format', 'fb2|epub');

$app->get('/qr-list', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    $books = $app['book.service']->fetchAll();
    $qrs = array();

    foreach ($books as $book) {
        $link = 'http://' . $request->server->get('SERVER_NAME') . '/book/' . $book->getId();

        $qrCode = new \Endroid\QrCode\QrCode();
        $qrCode->setText($link);
        $qrCode->setSize(300);
        $qrCode->setPadding(10);
        $qrCode->render($app['files_path'] . '/qr-' . $book->getId() . '.png');

        $qrs[] = array(
            'filename' => '/qr-' . $book->getId() . '.png',
            'book' => $book,
        );
    }

    return $app['twig']->render('qr-list.twig', array('qrs' => $qrs));
});

$app->get('/login', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {
    return $app['twig']->render('admin/login.twig', array(
        'error' => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
});

$app->get('/admin', function () use ($app) {
    /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flashBag */
    $flashBag = $app['session']->getFlashBag();

    return $app['twig']->render('admin/layout.twig', array(
        'content' => $app['twig']->render('admin/books.twig', array('books' => $app['book.service']->fetchAll())),
        'flash' => $flashBag->all(),
    ));
})->bind('admin');

$processBook = function (\Symfony\Component\HttpFoundation\Request $request, $method, $id) use ($app) {
    /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flashBag */
    $flashBag = $app['session']->getFlashBag();
    /** @var \Book\BookEntity $book */
    $book = ($id !== null) ? $app['book.service']->fetch($id) : new \Book\BookEntity();

    /** @var \Symfony\Component\Form\FormBuilder $formBuilder */
    $formBuilder = $app['form.factory']->createBuilder(new \Book\BookForm(), $book);
    $form = $formBuilder->getForm();
    $form->handleRequest($request);

    if ($form->isValid()) {
        $app['book.service']->save($book);
        $flashBag->add('success', 'запись изменена');

        return $app->redirect($app->url('admin_book_edit', array('id' => $book->getId(), 'method' => 'edit')));
    }

    return $app['twig']->render('admin/layout.twig', array(
        'content' => $app['twig']->render('admin/book-form.twig', array('form' => $form->createView(), 'book' => $book)),
        'flash' => $flashBag->all(),
    ));
};

$app->match('/admin/book/{method}/{id}', $processBook)->value('id', null)->assert('method', 'edit|create')->bind('admin_book_edit');

$app->get('/admin/book/delete/{id}', function (\Symfony\Component\HttpFoundation\Request $request, $id) use ($app) { // заменить на пост
    /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flashBag */
    $flashBag = $app['session']->getFlashBag();
    /** @var \Book\BookEntity $book */
    $book = $app['book.service']->fetch($id);

    if (!$book) {
        $flashBag->add('error', 'вы пытаетесь удалить несуществующую запись');

        return $app->redirect($app->url('admin'));
    }

    $app['book.service']->delete($book->getId());
    $flashBag->add('success', 'Запись ' . $book->getTitle() . ' удалена');

    return $app->redirect($app->url('admin'));
})->bind('admin_book_delete');

$app->post('/admin/upload/file', function (\Symfony\Component\HttpFoundation\Request $request) use ($app) {

    /** @var \Symfony\Component\Form\FormBuilder $formBuilder */
    $formBuilder = $app['form.factory']->createBuilder(new \Book\BookFileForm(), array());
    $form = $formBuilder->getForm();
    $form->handleRequest($request);

    $response = array(
        'success' => false,
        'name' => null,
        'size' => null,
    );

    if ($form->isValid()) {
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $form['x-files']->getData();

        $filename = 'file-' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($app['files_path'], $filename);
        $response = array(
            'success' => true,
            'name' => $filename,
            'size' => $file->getClientSize(),
        );
    }

    return $app->json($response);
});

$app->get('/admin/stats', function () use ($app) {
    /** @var \Symfony\Component\HttpFoundation\Session\Flash\FlashBag $flashBag */
    $flashBag = $app['session']->getFlashBag();

    $bookStats = $app['db']->fetchAll('SELECT count(*) AS cnt, b.title, b.author FROM download AS d LEFT JOIN book AS b ON b.id = d.id_book GROUP BY b.author, b.title');
    $platformStats = $app['db']->fetchAll('SELECT count(*) AS cnt, d.platform FROM download AS d GROUP BY d.platform');
    $browserStats = $app['db']->fetchAll('SELECT count(*) AS cnt, d.browser FROM download AS d GROUP BY d.browser');
    $formatStats = $app['db']->fetchAll('SELECT count(*) AS cnt, d.format FROM download AS d GROUP BY d.format');
    return $app['twig']->render('admin/layout.twig', array(
        'content' => $app['twig']->render('admin/stats.twig', array(
            'books' => $bookStats,
            'platforms' => $platformStats,
            'browsers' => $browserStats,
            'formats' => $formatStats,
        )),
        'flash' => $flashBag->all(),
    ));
});

$app->run();
$app['logtime']("last codeline\n");