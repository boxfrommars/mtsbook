<?php
/**
 * Created by PhpStorm.
 * User: xu
 * Date: 19.09.13
 * Time: 15:45
 */

namespace Book;


use Whale\System;

class BookEntity
{
    /** @var int */
    protected $_id;
    /** @var string */
    protected $_title;
    /** @var string */
    protected $_author;
    /** @var string */
    protected $_content;
    /** @var bool */
    protected $_isPublished;
    protected $_fileEpub;
    protected $_fileFb2;
    protected $_image;
    /** @var string */
    protected $_createdAt;
    /** @var string */
    protected $_updatedAt;

    protected $_dbFields = array(
        'title',
        'author',
        'content',

        array(
            'name' => 'is_published',
            'type' => \PDO::PARAM_BOOL
        ),

        'file_fb2',
        'file_epub',
        'image',

        'created_at',
        'updated_at'
    );

    public function __construct($data = array())
    {
        foreach ($data as $name => $value) {
            $method = 'set' . System::toCamelCase($name);
            $this->$method($value);
        }
    }


    public function raw(){
        $raw = array();
        foreach ($this->getDbFields() as $field) {
            if (!is_array($field)) {
                $field = array(
                    'name' => $field,
                    'type' => \PDO::PARAM_STR,
                );
            }

            $method = 'get' . System::toCamelCase($field['name']);

            $raw[$field['name']] = array(
                'value' => $this->$method(),
                'type' => $field['type']
            );
        }
        return $raw;
    }


    /**
     * @param array $dbFields
     */
    public function setDbFields($dbFields)
    {
        $this->_dbFields = $dbFields;
    }

    /**
     * @return array
     */
    public function getDbFields()
    {
        return $this->_dbFields;
    }

    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->_createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->_updatedAt = $updatedAt;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->_author = $author;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * @return mixed
     */
    public function getFileEpub()
    {
        return $this->_fileEpub;
    }

    /**
     * @param mixed $fileEpub
     */
    public function setFileEpub($fileEpub)
    {
        $this->_fileEpub = $fileEpub;
    }

    /**
     * @return mixed
     */
    public function getFileFb2()
    {
        return $this->_fileFb2;
    }

    /**
     * @param mixed $fileFb2
     */
    public function setFileFb2($fileFb2)
    {
        $this->_fileFb2 = $fileFb2;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->_image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->_image = $image;
    }

    /**
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->_isPublished;
    }

    /**
     * @param boolean $isPublished
     */
    public function setIsPublished($isPublished)
    {
        $this->_isPublished = $isPublished;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }
} 