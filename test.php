<?php 
/**
 * Test for MultiDB\DB
 *
 */

require_once 'PDOWrapper.php';

$settings = array(
    'blog' => array(
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'dbname' => 'blog',
    ),
    'store' => array(
        'host' => 'localhost',
        'user' => 'root',
        'password' => 'root',
        'dbname' => 'store',
    ),
);

// Get an instance of the PDO DB wrapper class
$db = new MultiDB\DB($settings);

// Inject the wrapper object into the object that
// needs to communicate with the database
$example = new Example($db);

$post = $example->getPostById(2);
var_dump($post);

$customers = $example->getCustomers();
var_dump($customers);

$search = $example->searchPosts('honey');
var_dump($search);

class Example {

    private $db;

    public function __construct($db=null) 
    {
        if ($db) {
            $this->db = $db;
        }
    }

    public function getPostById($id) 
    {
        $data = array(
            ':id' => $id, 
        );

        $this->db->prepare("
            SELECT *
            FROM posts 
            WHERE id = :id 
            ");

        $this->db->execute($data);

        return $this->db->fetch();
    }

    public function searchPosts($term='') 
    {
        $data = array(
            ':term' => '%'.$term.'%',
        );

        $this->db->prepare("
            SELECT *
            FROM posts
            WHERE title LIKE :term 
            LIMIT 10
            ");

        $this->db->execute($data);

        return $this->db->fetchAll();
    }

    public function getCustomers() 
    {
        $this->db->setConn('store');

        $this->db->prepare("SELECT * FROM customers");

        $this->db->setFetchMode(PDO::FETCH_BOTH);
        $this->db->execute();

        return $this->db->fetchAll();
    }

}

?>
