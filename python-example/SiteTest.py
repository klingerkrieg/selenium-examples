from random import randint
from time import sleep
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium import webdriver
import unittest



class SiteTestCase(unittest.TestCase):

    def setUp(self):
        #antes de cada teste
        pass

    def tearDown(self):
        #depois de cada teste
        pass

    @classmethod
    def setUpClass(self):
        #antes de todos os testes
        print ("Inicia os testes")
        global driver
        self.driver = driver
    
    @classmethod
    def tearDownClass(self):
        #Ao fim de todos os testes
        print ("Finaliza os testes")
        #apaga todos os dados
        self.driver.get('https://www2.ifrn.edu.br/sirab/testes-selenium/public/reset')
        self.driver.quit()

    def test_registro_ok(self):
        self.driver.get('https://www2.ifrn.edu.br/sirab/testes-selenium/public/register')
        
        
        id = randint(0,10000)

        nome_usuario = 'Teste com python %d' % id

        elem = self.driver.find_element(By.NAME, 'name')
        elem.send_keys(nome_usuario)

        elem = self.driver.find_element(By.ID, 'email')
        elem.send_keys('testepy%d@gmail.com' % id)

        elem = self.driver.find_element(By.ID, 'password')
        elem.send_keys('12345678')

        elem = self.driver.find_element(By.ID, 'password-confirm')
        elem.send_keys('12345678')

        form = self.driver.find_element(By.TAG_NAME, 'form')
        form.submit()

        # procura o botao do menu superior
        menu_superior = self.driver.find_element(By.CSS_SELECTOR ,'#navbarDropdown')

        # Exemplo de como pegar o texto do botao
        #print(menu_superior.text)

        # Confirma se o nome do usuário foi cadastrado corretamente
        self.assertEqual(nome_usuario, menu_superior.text)

        # Verifica se um elemento existe (Se está logado)
        exists = len(self.driver.find_elements(By.ID, "logout-form")) != 0
        self.assertTrue(exists)

        # Verifica se um texto existe
        #import pdb;pdb.set_trace();
        exists = self.driver.page_source.find("You are logged in!") > -1
        self.assertTrue(exists)

        # Clica no menu superior
        menu_superior.click()

        # Clica em logout
        logout = self.driver.find_element(By.ID,'logout')
        logout.click()

        # Verifica se foi deslogado
        # Se o form nao existir mais é porque está deslogado
        exists = len(self.driver.find_elements(By.ID, "logout-form")) != 0
        self.assertFalse(exists)

    def test_registro_fail(self):

        self.driver.get('https://www2.ifrn.edu.br/sirab/testes-selenium/public/register')
        
        #submete o form sem preencher nada
        form = self.driver.find_element(By.TAG_NAME, 'form')
        form.submit()

        #checa as mensagens de erro
        errMsg = self.driver.find_element(By.XPATH,"//*[ text() = 'The name field is required.' ]");
        self.assertIsNotNone(errMsg)
        errMsg = self.driver.find_element(By.XPATH,"//*[ text() = 'The email field is required.' ]");
        self.assertIsNotNone(errMsg)
        errMsg = self.driver.find_element(By.XPATH,"//*[ text() = 'The password field is required.' ]");
        self.assertIsNotNone(errMsg)


if __name__ == '__main__':
    svc = Service("./chromedriver.exe")
    driver = webdriver.Chrome(service=svc)
    try:
        unittest.main(verbosity=2)
    except:
        driver.get_screenshot_as_file("screenshot.png")