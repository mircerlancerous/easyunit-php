<?php
/*
Copyright 2021 Mircerlancerous - https://github.com/mircerlancerous/easyunit-php

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
*/

//This class is a sample of what your unit test class should look like
class SampleTests{
	public static function PassingTests(){
		
		//Include any files you need for your test here if you didn't already
		
		$check = $val = "abc is def";
		TestExecuter::AssertAreEqual($check, $val);

		$check = $val = 157382;
		TestExecuter::AssertAreEqual($check, $val);

		$check = $val = (4763 - 324) . " seconds";
		TestExecuter::AssertAreEqual($check, $val);
	}
	
	public static function FailingTests(){
		//This test will pass
		$check = $val = 123;
		TestExecuter::AssertAreEqual($check, $val);

		//But this test will fail
		$val = "not a number";
		TestExecuter::AssertAreEqual($check, $val);

		//And this one is fine too
		$check = $val;
		TestExecuter::AssertAreEqual($check, $val);
	}
}

//This is the class that handles all the tests
//Include the tests in this file as in above, or include one or more separate files here
class TestExecuter{
	//This is a list of all classes that contain tests
	//Tests are static methods within the listed classes
	private $TestClasses = [
		"SampleTests"
	];
	
	public static function ExecuteTest($test){
		if(!$test){
			//run all tests
			$obj = new TestExecuter();
			$testlist = $obj->GetTestList();
			$result = "success";
			foreach($testlist as $key => $tests){
				foreach($tests as $testname){
					$check = self::ExecuteTest($key."_".$testname);
					if($check != "success"){
						$result = $check;
						break;
					}
				}
			}
			return $result;
		}
		list($key, $testname) = explode("_", $test);
		
		//run the test
		try{
			$key::$testname();
			$result = "success";
		}
		catch(Exception $e){
			$result = "<b>$test</b> ".$e->getMessage();
		}
		return $result;
	}
	
	public function GetTestList(){
		$list = [];
		foreach($this->TestClasses as $key){
			$list[$key] = get_class_methods($key);
		}
		return $list;
	}
	
	/**********************************/
	//Assertion methods; add more as your needs require
	
	public static function AssertAreEqual($expected, $checkval){
		if($expected === $checkval){
			return;
		}
		//values don't match so return some details
		throw new Exception("failed<br/><b>Expected</b><br/><pre>$expected</pre><b>Actual</b><pre>$checkval</pre>");
	}
	
	public static function AssertAreNotEqual($expected, $checkval){
		if($expected !== $checkval){
			return;
		}
		//values match so return some details
		throw new Exception("failed<br/><b>Expected</b><br/><pre>$expected</pre><b>Actual</b><i>Identical: not equal was expected</i>");
	}
	
	/**********************************/
}

//check if one or more tests are set to be run
if(isset($_GET['test'])){
	//there is at least one test, so run and exit
	
	$test = $_GET['test'];
	if(!$test){
		echo "<h3>Test: Run All</h3>";
	}
	else{
		echo "<h3>Test: $test</h3>";
	}
	
	//prepare for the test
	$starttime = microtime(true);
	$result = TestExecuter::ExecuteTest($test);
	//output the result and time elapsed
	echo "<h4>Elapsed: ".round(microtime(true) - $starttime, 6)." s</h4>";
	echo "Result:<br/>".$result;
	exit;
}
//no test(s), so show the unit test interface
?><!doctype html>
<html>
	<head>
		<title>Unit Tests</title>
		<meta charset="utf-8"/>
		<style type="text/css">
			.righttd{
				text-align: center;
			}
			table{
				width: 100%;
			}
			td{
				vertical-align: top;
			}
			iframe{
				margin-left: 25px;
				min-height: 500px;
			}
		</style>
	</head>
	<body>
		<h1>Unit Tests</h1>
		<a href="?test" target="results">Run All</a>
		<br/>
		<table><tr><td>
			<?php
			$exec = new TestExecuter();
			$list = $exec->GetTestList();
			foreach($list as $key => $tests){
				echo "<h2>$key</h2>";
				foreach($tests as $test){
					echo ": <a href='?test=".urlencode($key."_".$test)."' target='results'>$test</a><br/>";
				}
			}
			?>
		</td><td class="righttd">
			<iframe name="results"></iframe>
		</td></tr></table>
	</body>
</html>