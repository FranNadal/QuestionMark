<?php
require_once("core/Database.php");
require_once("core/FilePresenter.php");
require_once("core/MustachePresenter.php");
require_once("core/Router.php");

require_once("controller/InicioController.php");
require_once("controller/HomeController.php");
require_once("controller/RegisterController.php");
require_once("controller/LoginController.php");
require_once("controller/PerfilController.php");
require_once("controller/JugarController.php");
require_once("controller/ApiController.php");
require_once("controller/RankingController.php");
require_once ("controller/EditorController.php");

require_once("model/HomeModel.php");
require_once("model/RegisterModel.php");
require_once("model/LoginModel.php");
require_once("model/PerfilModel.php");
require_once("model/JugarModel.php");
require_once("model/RankingModel.php");
require_once ("model/EditorModel.php");
include_once('vendor/mustache/src/Mustache/Autoloader.php');

class Configuration
{
    public function getDatabase()
    {
        $config = $this->getIniConfig();

        return new Database(
            $config["database"]["server"],
            $config["database"]["user"],
            $config["database"]["dbname"],
            $config["database"]["pass"]
        );
    }

    public function getIniConfig()
    {
        return parse_ini_file(__DIR__ . "/configuration/config.ini", true);
    }

    public function getApiController()
    {
        return new ApiController();
    }

    public function getInicioController()
    {
        return new InicioController($this->getViewer());
    }

    public function getHomeController()
    {
        return new HomeController(new HomeModel($this->getDatabase()), $this->getViewer());
    }
    public function getEditorController()
    {
        return new EditorController(new EditorModel($this->getDatabase()), $this->getViewer());
    }

    public function getRankingController()
    {
        return new RankingController(new RankingModel($this->getDatabase()), $this->getViewer());
    }

    public function getPerfilController()
    {
        return new PerfilController(new PerfilModel($this->getDatabase()), $this->getViewer());
    }

    public function getJugarController()
    {
        return new JugarController(new JugarModel($this->getDatabase()), $this->getViewer());
    }

    public final function getRegisterController(){
        return new RegisterController(new RegisterModel ($this->getDatabase()),$this->getViewer());
    }

    public final function getLoginController(){
        return new LoginController(new LoginModel ($this->getDatabase()),$this->getViewer());
    }
    public function getRouter()
    {
        return new Router("getInicioController", "show", $this);
    }

    public function getViewer()
    {
        //return new FileView();
        return new MustachePresenter("view");
    }
}