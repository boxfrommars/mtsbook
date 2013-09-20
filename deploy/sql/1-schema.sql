DROP TABLE "book" CASCADE;
DROP TABLE "download" CASCADE;

CREATE TABLE "book" (
  "id" BIGSERIAL NOT NULL,
  "is_published" BOOL DEFAULT 't',

  "author" VARCHAR(255) NOT NULL,
  "title" VARCHAR(255) NOT NULL,
  "content" TEXT,
  "image" VARCHAR (255),

  "file_epub" VARCHAR(255),
  "file_fb2" VARCHAR(255),

  "created_at" TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NOW(),
  "updated_at" TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NOW(),

  PRIMARY KEY("id")
);
/*
 'platform' => '[Detected Platform]',
    'browser'  => '[Detected Browser]',
    'version' */

CREATE TABLE "download" (
  "id" BIGSERIAL NOT NULL,
  "id_book" BIGINT REFERENCES book (id) ON DELETE CASCADE,
  "format" VARCHAR (255),

  "platform" VARCHAR (255),
  "browser" VARCHAR (255),
  "version" VARCHAR (255),

  PRIMARY KEY ("id")
);

INSERT INTO book (author, title, content) VALUES ('Гилберт Кит Честертон', 'Жив-человек', 'Некий чудак Инносент появляется в тихом пансионе "Маяк" и переворачивает всю его жизнь. "Жив человек" Честертона: история о тождестве радости и праведности.');
INSERT INTO book (author, title, content) VALUES ('Алан Брэдли', 'Сладость на корочке пирога', 'В старинном английском поместье Букшоу обитают последние представители аристократического рода — эксцентричный полковник де Люс и три его дочери.');
INSERT INTO book (author, title, content) VALUES ('Джеймс Крюс', 'Тим Талер, или Проданный смех', 'Когда-то в бедном квартале одного немецкого города жил обычный мальчишка Тим Талер. Его задорная улыбка и заразительный смех помогали ему и его друзьям, идти по жизни несмотря ни на какие трудности.');


