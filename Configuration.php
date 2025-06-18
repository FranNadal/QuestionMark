<?php
require_once("core/Database.php");
require_once("core/FilePresenter.php");
require_once("core/MustachePresenter.php");
require_once("core/Router.php");

require_once("controller/HomeController.php");
require_once("controller/GroupController.php");
require_once("controller/SongController.php");
require_once("controller/TourController.php");
require_once("controller/RegisterController.php");
require_once("controller/LoginController.php");
require_once("controller/PerfilController.php");
require_once("controller/JugarController.php");
require_once("controller/ApiController.php");



require_once("model/GroupModel.php");
require_once("model/SongModel.php");
require_once("model/TourModel.php");
require_once("model/RegisterModel.php");
require_once("model/LoginModel.php");
require_once("model/PerfilModel.php");
require_once("model/JugarModel.php");
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

    public function getSongController()
    {
        return new SongController(
            new SongModel($this->getDatabase()),
            $this->getViewer()
        );
    }
    public function getApiController()
    {
        return new ApiController();
    }
    public function getTourController()
    {
        return new TourController(
            new TourModel($this->getDatabase()),
            $this->getViewer()
        );
    }

    public function getHomeController()
    {
        return new HomeController($this->getViewer());
    }

    public function getPerfilController()
    {
        return new PerfilController(new PerfilModel($this->getDatabase()), $this->getViewer());
    }

    public function getJugarController()
    {
        return new JugarController(new JugarModel($this->getDatabase()), $this->getViewer());
    }

    public function getGroupController()
    {
        return new GroupController(new GroupModel($this->getDatabase()), $this->getViewer());
    }
    public final function getRegisterController(){
        return new RegisterController(new RegisterModel ($this->getDatabase()),$this->getViewer());
    }

    public final function getLoginController(){
        return new LoginController(new LoginModel ($this->getDatabase()),$this->getViewer());
    }
    public function getRouter()
    {
        return new Router("getHomeController", "show", $this);
    }

    public function getViewer()
    {
        //return new FileView();
        return new MustachePresenter("view");
    }
}