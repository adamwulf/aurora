<?
Class TestAvalanche_HashArray extends TestCase { 

   public function test_new_table() {
	$hash = new HashTable();
	$my_array = $hash->enum();

	$this->assert(is_object($hash), "make a new hashtable" );
   }

   public function test_new_table_no_elements() {
	$hash = new HashTable();
	$my_array = $hash->enum();

	$this->assertEquals(count($my_array), 0, "a hashtable with no elements" );
   }

   public function test_new_table_one_element() {
	$hash = new HashTable();
	$hash->put("hash value", new HashTable());
	$my_array = $hash->enum();

	$this->assertEquals(count($my_array), 1, "a hash table with one element" );
   }

   public function test_new_table_get() {
	$hash = new HashTable();
	$hash->put("hash value", new HashTable());
	$table = $hash->get("hash value");

	$this->assert(is_object($table), "retrieve value from hashtable" );
   }

   public function test_new_table_clear() {
	$hash = new HashTable();
	$hash->put("hash value", new HashTable());
	$hash->clear("hash value");
	$my_array = $hash->enum();

	$this->assertEquals(count($my_array), 0, "clear the hashtable of a value" );
   }

   public function test_new_table_clear_miss() {
	$hash = new HashTable();
	$hash->put("hash value", new HashTable());
	$hash->clear("hash values");
	$my_array = $hash->enum();

	$this->assertEquals(count($my_array), 1, "try to clear a value that doesn't exist in table" );
   }

   public function test_new_table_get_miss() {
	$hash = new HashTable();
	$hash->get("hash value");
	$my_array = $hash->enum();

	$this->assertEquals(count($my_array), 0, "try to get a value that doesn't exist in table" );
   }

   public function test_new_table_reset() {
	$hash = new HashTable();
	$hash->put("hash value", new HashTable());
	$hash->reset();
	$my_array = $hash->enum();

	$this->assertEquals(count($my_array), 0, "reset the hashtable" );
   }
};


?>