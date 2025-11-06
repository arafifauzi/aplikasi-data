<?php
// controllers/HomeController.php
require_once 'models/News.php';
require_once 'models/User.php';

class HomeController
{
    private $newsModel;
    private $userModel;

    public function __construct()
    {
        $this->newsModel = new News();
        $this->userModel = new User();
    }

    public function index()
    {
        $latest_news = $this->newsModel->getLatestNews(5);
        $popular_news = $this->newsModel->getPopularNews(4);
        $latest_news = $latest_news ?: [];
        $popular_news = $popular_news ?: [];
        require_once 'views/home.php';
    }
}
?>