<?php

abstract class BasePage
{
    protected string $title = "";
    protected ?string $status = null;
    protected Staff $user;

    protected function prepare() : void
    {}

    protected function sendHttpHeaders() : void
    {}

    protected function extraHTMLHeaders() : string
    {
        return "";
    }

    protected function pageHeader(?string $user = null) : string
    {
        $m = MustacheProvider::get();
        return $m->render('header',["userName" => $user]);
    }

    abstract protected function pageBody();

    protected function pageFooter() : string
    {
        $m = MustacheProvider::get();
        return $m->render('footer',[]);
    }

    public function render() : void
    {
        try
        {
            if(!isset($_SESSION))
                session_start();
            $this->prepare();
            if(!isset($_SESSION['user'])){
                http_response_code(401);
                header('Location: ../login.php');
                $data = [
                    'lang' => AppConfig::get('app.lang'),
                    'title' => $this->title,
                    'pageHeader' => $this->pageHeader(),
                    'pageBody' => $this->pageBody(),
                    'pageFooter' => $this->pageFooter()
                ];
            }

            else{
                $data = [
                    'lang' => AppConfig::get('app.lang'),
                    'title' => $this->title,
                    'pageHeader' => $this->pageHeader($_SESSION["surname"]." ".$_SESSION["name"]),
                    'pageBody' => $this->pageBody(),
                    'pageFooter' => $this->pageFooter()
                ];

            }
            $m = MustacheProvider::get();
            $this->sendHttpHeaders();
            echo $m->render("page", $data);
        }

        catch (BaseException $e)
        {
            $exceptionPage = new ExceptionPage($e);
            $exceptionPage->render();
            exit;
        }

        catch (Exception $e)
        {
            if (AppConfig::get('debug'))
                throw $e;

            $e = new BaseException("Server error", 500);
            $exceptionPage = new ExceptionPage($e);
            $exceptionPage->render();
            exit;
        }
    }
}