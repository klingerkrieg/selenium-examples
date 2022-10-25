<?php
namespace Facebook\WebDriver;

require_once('vendor/autoload.php');

use Exception;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use PHPUnit\Framework\TestCase;




#https://github.com/php-webdriver/php-webdriver
#https://phpunit.readthedocs.io/pt_BR/latest/

$serverUrl = 'http://localhost:4444';
$driver = RemoteWebDriver::create($serverUrl, DesiredCapabilities::chrome());


#Toda classe tem que estender TestCase e termina com 'Test"
#O nome da classe CamelCaseTest tem que ser o mesmo nome do arquivo
class SiteTest extends TestCase {

	public static $driver;

	#Configurações iniciais para todos os testes
	protected function setUp(): void {
		#Antes de cada teste
		print "INI";
	}
	
	protected function tearDown() : void {
		#Apos cada teste
		print "FIM";
	}

	public static function setUpBeforeClass(): void {
		global $driver;
		SiteTest::$driver = $driver;
    }

    public static function tearDownAfterClass(): void {
        print "Finalizando testes";
		#apaga todos os dados
		SiteTest::$driver->get('https://www2.ifrn.edu.br/sirab/testes-selenium/public/reset');
		SiteTest::$driver->quit();
    }

	#Todo teste tem que começar com "test..."
    public function testRegistroOk(){
		try{
			// Abre o endereço
			SiteTest::$driver->get('https://www2.ifrn.edu.br/sirab/testes-selenium/public/register');

			$idTest = rand(0,1000);

			$nomeUsuario = "Teste automatico php $idTest";

			// Preenche os campos
			SiteTest::$driver->findElement(WebDriverBy::id('name'))
				->sendKeys($nomeUsuario);

			SiteTest::$driver->findElement(WebDriverBy::id('email'))
				->sendKeys("testephp$idTest@gmail.com");

			SiteTest::$driver->findElement(WebDriverBy::id('password'))
				->sendKeys('12345678');

			SiteTest::$driver->findElement(WebDriverBy::id('password-confirm'))
				->sendKeys('12345678')
				->submit();

			# procura o botao do menu superior
			$menuSuperior = SiteTest::$driver->findElement(
				WebDriverBy::cssSelector('#navbarDropdown')
			);

			# Exemplo de como pegar o texto do botao
			#print $menuSuperior->getText();

			# Confirma se o nome do usuário foi cadastrado corretamente
			$this->assertSame($nomeUsuario, $menuSuperior->getText());

			# Verifica se um elemento existe (Se está logado)
			$exists = count(SiteTest::$driver->findElements(WebDriverBy::id("logout-form"))) != 0;
            $this->assertTrue($exists);

			# Verifica se um texto existe
            $exists = strpos(SiteTest::$driver->getPageSource(), "You are logged in!") > -1;
            $this->assertTrue($exists);

			# Clica no menu superior
			$menuSuperior->click();
			
			# Clica em logout
			$logout = SiteTest::$driver->findElement(WebDriverBy::id('logout'));
			$logout->click();

			# Verifica se foi deslogado
            # Se o form nao existir mais é porque está deslogado
            $exists = count(SiteTest::$driver->findElements(WebDriverBy::id("logout-form"))) != 0;
            $this->assertFalse($exists);

		} catch (Exception $x) {
			print $x;
			#tira foto
			SiteTest::$driver->takeScreenshot('screenshot.png');
			
		}
	}


	public function testRegistroFail(){
		try{
			$url = "https://www2.ifrn.edu.br/sirab/testes-selenium/public/register";
			// Abre o endereço
			SiteTest::$driver->get($url);

			SiteTest::$driver->takeScreenshot('screenshot.png');
			// Nao preenche nenhum campo, apenas submete
			SiteTest::$driver->findElement(WebDriverBy::tagName('form'))->submit();

			$el = SiteTest::$driver->findElement(WebDriverBy::xpath("//*[ text() = 'The name field is required.' ]"));
			$this->assertNotNull($el);

			$el = SiteTest::$driver->findElement(WebDriverBy::xpath("//*[ text() = 'The email field is required.' ]"));
			$this->assertNotNull($el);

			$el = SiteTest::$driver->findElement(WebDriverBy::xpath("//*[ text() = 'The password field is required.' ]"));
			$this->assertNotNull($el);
		} catch (Exception $x) {
			print $x;
			#tira foto
			SiteTest::$driver->takeScreenshot('screenshot.png');
			
		}
	}
}

#vendor\bin\phpunit SiteTest.php