<?php
/**
 * @author Dmitry Groza <boxfrommars@gmail.com>
 */

namespace Book;

use Doctrine\DBAL\Connection;

class BookService {

    /** @var  Connection */
    protected $_db;

    /** @var string table name */
    protected $_name = 'book';

    /** @var string table sequence */
    protected $_seq = 'book_id_seq';

    /**
     * @param Connection $db
     * @param array $options
     */
    public function __construct($db, $options = array()) {
        $this->setDb($db);
        if (array_key_exists('name', $options)) $this->setName($options['name']);
        if (array_key_exists('seq', $options)) $this->setSeq($options['seq']);
    }

    /**
     * @return array
     */
    public function fetchAll() {
        $qb = $this->getQuery();
        $qb->execute();
        $items = $this->getDb()->executeQuery($qb)->fetchAll();
        return $this->_createEntities($items);
    }

    /**
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getQuery() {
        /** @var \Doctrine\DBAL\Query\QueryBuilder $qb */
        $qb = $this->getDb()->createQueryBuilder();

        $qb->select("b.*")
            ->from('book', 'b')
            ->orderBy('title');

        return $qb;
    }


    public function fetch($id) {
        $qb = $this->getQuery();
        $qb->add('where', 'b.id = :id');
        $item = $this->getDb()->executeQuery($qb, array('id' => $id))->fetch();;
        return $item ? $this->_createEnity($item) : false;
    }

    /**
     * @param BookEntity $book
     */
    public function insert($book) {
        $data = array();
        $types = array();

        foreach($book->raw() as $fieldName => $field) {
            $data[$fieldName] = $field['value'];
            $types[$fieldName] = $field['type'];
        }

        $this->getDb()->insert($this->getName(), $data, $types);
        $book->setId($this->_db->lastInsertId($this->getSeq()));
    }

    /**
     * @param BookEntity $book
     */
    public function update($book) {
        $data = array();
        $types = array();

        foreach($book->raw() as $fieldName => $field) {
            $data[$fieldName] = $field['value'];
            $types[$fieldName] = $field['type'];
        }
        $this->getDb()->update($this->getName(), $data, array('id' => $book->getId()), $types);
    }

    /**
     * @param BookEntity $book
     */
    public function save($book) {
        null === $book->getId() ? $this->insert($book) : $this->update($book);
    }

    protected function _createEnity($data) {
        return new BookEntity($data);
    }

    protected function _createEntities($data) {
        return array_map(function ($item){ return new BookEntity($item); }, $data);
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param string $seq table sequence
     */
    public function setSeq($seq)
    {
        $this->_seq = $seq;
    }

    /**
     * @return string table sequence
     */
    public function getSeq()
    {
        return $this->_seq;
    }

    /**
     * @param Connection $db
     */
    public function setDb($db)
    {
        $this->_db = $db;
    }

    /**
     * @return Connection
     */
    public function getDb()
    {
        return $this->_db;
    }

}