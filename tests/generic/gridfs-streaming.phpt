--TEST--
GridFS: Testing reading the whole file
--SKIPIF--
<?php if (getenv('SKIP_SLOW_TESTS')) die('skip slow tests excluded by request'); ?>
<?php require_once "tests/utils/standalone.inc" ?>
--FILE--
<?php
require_once "tests/utils/server.inc";
$dsn = MongoShellServer::getStandaloneInfo();
$conn = new MongoClient($dsn);
$db   = $conn->selectDb('admin');
$grid = $db->getGridFs('wrapper');

// delete any previous results
$grid->drop();

// dummy file
$bytes = "";
for ($i=0; $i < 200*1024; $i++) {
    $bytes .= sha1(rand(1, 1000000000));
}
$grid->storeBytes($bytes, array("filename" => "demo.txt"));

// fetch it
$file = $grid->findOne(array('filename' => 'demo.txt'));

// get file descriptor
$fp = $file->getResource();

$tmp = "";
$i=0;
while (!feof($fp)) {
    $tmp .= ($t=fread($fp, rand(1024,8024)));
}
var_dump($bytes === $tmp);
?>
--EXPECTF--
bool(true)
