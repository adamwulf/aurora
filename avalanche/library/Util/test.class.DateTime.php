<?
Class TestDateTime extends TestCase{

	public function test_DateTime_obj(){
		$d = new MMDateTime("2004-12-23 12:23:14");

		$this->assertEquals($d->year(), 2004, "info is correct");
		$this->assertEquals($d->month(), 12, "info is correct");
		$this->assertEquals($d->day(), 23, "info is correct");
		$this->assertEquals($d->hour(), 12, "info is correct");
		$this->assertEquals($d->minute(), 23, "info is correct");
		$this->assertEquals($d->second(), 14, "info is correct");

		$d->month(32);

		$this->assertEquals($d->month(), 32, "info is correct");
	}


	public function testToGMT(){
		$d = new MMDateTime("2004-08-04 20:06:14");
		$d->toGMT(-6);

		$this->assertEquals($d->year(), 2004, "info is correct");
		$this->assertEquals($d->month(), 8, "info is correct");
		$this->assertEquals($d->day(), 5, "info is correct");
		$this->assertEquals($d->hour(), 1, "info is correct");
		$this->assertEquals($d->minute(), 6, "info is correct");
		$this->assertEquals($d->second(), 14, "info is correct");

	}

	public function testToGMTToTimezone(){
		$d = new MMDateTime("2004-08-04 20:06:14");
		$d->toGMT(-6);
		$d = new MMDateTime($d->toString());
		$d->toTimezone(-6);

		$this->assertEquals($d->year(), 2004, "info is correct");
		$this->assertEquals($d->month(), 8, "info is correct");
		$this->assertEquals($d->day(), 4, "info is correct");
		$this->assertEquals($d->hour(), 20, "info is correct");
		$this->assertEquals($d->minute(), 6, "info is correct");
		$this->assertEquals($d->second(), 14, "info is correct");

	}

	public function testToHoustonTimezone(){
		$d = new MMDateTime("2004-08-05 01:06:14");
		$d->toTimezone(-6);

		$this->assertEquals($d->year(), 2004, "info is correct");
		$this->assertEquals($d->month(), 8, "info is correct");
		$this->assertEquals($d->day(), 4, "info is correct");
		$this->assertEquals($d->hour(), 20, "info is correct");
		$this->assertEquals($d->minute(), 6, "info is correct");
		$this->assertEquals($d->second(), 14, "info is correct");

	}

	public function testToBerlinTimezone(){
		$d = new MMDateTime("2004-08-05 01:06:14");
		$d->toTimezone(1);

		$this->assertEquals($d->year(), 2004, "info is correct");
		$this->assertEquals($d->month(), 8, "info is correct");
		$this->assertEquals($d->day(), 5, "info is correct");
		$this->assertEquals($d->hour(), 3, "info is correct");
		$this->assertEquals($d->minute(), 6, "info is correct");
		$this->assertEquals($d->second(), 14, "info is correct");
	}
};


?>