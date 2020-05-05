<?php
require_once("model/Moto.php");
interface MotoStorage {
	public function read($id);
	public function readAll();
	public function create(Moto $a);
	public function delete($id);
	public function update($id,Moto $a);
}

?>