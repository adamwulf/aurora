<?
Class TestSQLCache extends TestCase{ 

	public function test_SQLCache_obj(){
		$c = new SQLCache();
		
		$result = "this is a sample result";
		$o_result = "other result";
		$t_result = "third result";
		
		$sql   = "SELECT * FROM avalanche_buddy WHERE 1";
		$other = "SELECT * FROM avalanche_buddy WHERE billy = 'yourmom'";
		$third = "SELECT * FROM avalanche_third WHERE 1";
		$this->assertEquals(SQLCache::getTableName($sql), "avalanche_buddy", "the table is correct");
		$this->assertEquals(SQLCache::getTableName($other), "avalanche_buddy", "the table is correct");
		$this->assertEquals(SQLCache::getTableName($third), "avalanche_third", "the table is correct");

		$c->put($sql, $result);
		$c->put($other, $o_result);
		$c->put($third, $t_result);
		
		$this->assertEquals($c->get($sql), $result, "the result is correct");
		$this->assertEquals($c->get($other), $o_result, "the result is correct");
		$this->assertEquals($c->get($third), $t_result, "the result is correct");
		$c->clear($sql);
		$this->assertEquals($c->get($sql), false, "the result is correct");
		$this->assertEquals($c->get($other), $o_result, "the result is correct");
		$this->assertEquals($c->get($third), $t_result, "the result is correct");
	}	


	public function test_SQLCache_Update(){
		$c = new SQLCache();
		
		$result = "this is a sample result";
		$o_result = "other result";
		$t_result = "third result";
		
		$sql = "SELECT * FROM avalanche_buddy WHERE 1";
		$other = "SELECT * FROM avalanche_buddy WHERE billy = 'yourmom'";
		$third = "SELECT * FROM avalanche_third WHERE 1";
		$update = "UPDATE avalanche_buddy SET `asdf` = 'asdf'";
		$this->assertEquals(SQLCache::getTableName($sql), "avalanche_buddy", "the table is correct");
		$this->assertEquals(SQLCache::getTableName($update), "avalanche_buddy", "the table is correct");
		
		$c->put($sql, $result);
		$c->put($other, $o_result);
		$c->put($third, $t_result);
		$c->clear($update);
		$this->assertEquals($c->get($sql), false, "the result is correct");
		$this->assertEquals($c->get($other), false, "the result is correct");
		$this->assertEquals($c->get($third), $t_result, "the result is correct");
	}	


	public function test_SQLCache_Insert(){
		$c = new SQLCache();
		
		$result = "this is a sample result";
		$sql = "SELECT * FROM avalanche_buddy WHERE 1";
		$other = "SELECT * FROM avalanche_buddy WHERE billy = 'yourmom'";
		$third = "SELECT * FROM avalanche_third WHERE 1";
		$insert = "INSERT INTO avalanche_buddy (`asf`) VALUES ('asdf')";
		$this->assertEquals(SQLCache::getTableName($sql), "avalanche_buddy", "the table is correct");
		$this->assertEquals(SQLCache::getTableName($insert), "avalanche_buddy", "the table is correct");
		
		$c->put($sql, $result);
		$c->put($other, $o_result);
		$c->put($third, $t_result);
		$c->clear($insert);
		$this->assertEquals($c->get($sql), false, "the result is correct");
		$this->assertEquals($c->get($other), false, "the result is correct");
		$this->assertEquals($c->get($third), $t_result, "the result is correct");
	}	
};


?>