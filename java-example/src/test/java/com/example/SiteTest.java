package com.example;

import java.awt.image.BufferedImage;
import java.io.File;
import java.io.IOException;
import org.apache.log4j.Logger;
import org.apache.log4j.spi.LoggerFactory;

import java.util.Random;

import javax.imageio.ImageIO;

import org.junit.AfterClass;
import org.junit.Assert;
import org.junit.Before;
import org.junit.BeforeClass;
import org.junit.Test;
import org.openqa.selenium.By;
import org.openqa.selenium.OutputType;
import org.openqa.selenium.TakesScreenshot;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.chrome.ChromeDriver;

public class SiteTest {

    static WebDriver driver;
    public static Logger log = Logger.getLogger(SiteTest.class.getName());
    
    @BeforeClass
    public static void initAntesDeTudo() {
        System.setProperty("webdriver.chrome.driver","./chromedriver.exe");
        //Cria o driver
        driver = new ChromeDriver();
        System.out.println("INICIA");
        System.out.flush();
    }

    @AfterClass
    public static void finaliza(){
        //limpa tudo
        driver.get("https://www2.ifrn.edu.br/sirab/testes-selenium/public/reset");
        driver.quit();
        System.out.println("FINALIZA");
        System.out.flush();
    }

    @Test
    public void testRegisterOk() {
        try {
            //Abre uma página
            driver.get("https://www2.ifrn.edu.br/sirab/testes-selenium/public/register");

            Random rand = new Random();
            int id = rand.nextInt();
            String name = "Teste automatico java " + id;

            //encontra e preenche um elemento
            driver.findElement(By.id("name")).sendKeys(name);
            driver.findElement(By.id("email")).sendKeys("java"+id+"@gmail.com");
            driver.findElement(By.id("password")).sendKeys("12345678");
            driver.findElement(By.id("password-confirm")).sendKeys("12345678");
            driver.findElement(By.tagName("form")).submit();
            
            WebElement menuSuperior = driver.findElement(By.cssSelector("#navbarDropdown"));

            // Exemplo de como pegar o texto do botao
            //System.out.println(menuSuperior.getText());
            
            // Confirma se o nome do usuário foi cadastrado corretamente
            Assert.assertEquals(name, menuSuperior.getText());
            
            // Verifica se um elemento existe (Se está logado)
            boolean exists = driver.findElements(By.id("logout-form")).size() != 0;
            Assert.assertTrue(exists);
            
            //Verifica se um texto existe
            exists = driver.getPageSource().contains("You are logged in!");
            Assert.assertTrue(exists);

            //Clica no menu superior
            menuSuperior.click();

            //Clica em logout
            WebElement logout = driver.findElement(By.id("logout"));
            logout.click();

            //Verifica se foi deslogado
            //Se o form nao existir mais é porque está deslogado
            exists = driver.findElements(By.id("logout-form")).size() != 0;
            Assert.assertFalse(exists);
        } catch (Exception e) {
            System.out.println("!!!!!!!!!!!Erro:!!!!!!!!!!!");
            System.out.println(e.getMessage());
            System.out.flush();
            takesScreenshot();
        }   
    }



    @Test
    public void testRegisterFail() {

        try {
            driver.get("https://www2.ifrn.edu.br/sirab/testes-selenium/public/register");
            driver.findElement(By.tagName("form")).submit();

            WebElement el = driver.findElement(By.xpath("//*[ text() = 'The name field is required.' ]"));
            Assert.assertNotNull(el);
            el = driver.findElement(By.xpath("//*[ text() = 'The email field is required.' ]"));
            Assert.assertNotNull(el);
            el = driver.findElement(By.xpath("//*[ text() = 'The password field is required.' ]"));
            Assert.assertNotNull(el);
        } catch (Exception e) {
            System.out.println("!!!!!!!!!!!Erro:!!!!!!!!!!!");
            System.out.println(e.getMessage());
            System.out.flush();
            takesScreenshot();
        }
    }

    public void takesScreenshot(){
        File file = ((TakesScreenshot) driver).getScreenshotAs(OutputType.FILE);
            
        BufferedImage bImage;
        try {
            bImage = ImageIO.read(file);
            ImageIO.write(bImage, "png", new File("./screenshot.png"));
        } catch (IOException e1) {
            // TODO Auto-generated catch block
            e1.printStackTrace();
        }
    }
}
