<?php
namespace EllisLab\Tests\ExpressionEngine\Controllers\Utilities;

class QueryTest extends \PHPUnit_Framework_TestCase {

	public static function setUpBeforeClass()
	{
		require_once(APPPATH.'core/Controller.php');
	}

	public function testRoutableMethods()
	{
		$controller_methods = array();

		foreach (get_class_methods('EllisLab\ExpressionEngine\Controller\Utilities\Query') as $method)
		{
			$method = strtolower($method);
			if (strncmp($method, '_', 1) != 0)
			{
				$controller_methods[] = $method;
			}
		}

		sort($controller_methods);

		$this->assertEquals(array('index', 'runquery'), $controller_methods);
	}

}