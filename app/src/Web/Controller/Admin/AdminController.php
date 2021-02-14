<?php

namespace Fc2blog\Web\Controller\Admin;

use Fc2blog\Config;
use Fc2blog\Web\Controller\AppController;
use Fc2blog\Web\Request;
use Fc2blog\Web\Session;

abstract class AdminController extends AppController
{
  protected function beforeFilter(Request $request)
  {
    // 親のフィルター呼び出し
    parent::beforeFilter($request);

    // install.lockファイルがなければインストーラーへ
    if (!$this->isInstalled() && (
        $request->className !== CommonController::class ||
        $request->methodName !== 'install'
      )) {
      $this->redirect($request, ['controller' => 'Common', 'action' => 'install']);
    }

    if (!$this->isLogin($request)) {
      // 未ログイン時は新規登録とログイン以外させない
      $allows = array(
        UsersController::class => array('login', 'register'),
        CommonController::class => array('lang', 'install'),
      );
      $controller_name = $request->className;
      $action_name = $request->methodName;
      if (!isset($allows[$controller_name]) || !in_array($action_name, $allows[$controller_name])) {
        $this->redirect($request, array('controller' => 'Users', 'action' => 'login'));
      }
      return;
    }

    if (!$this->isSelectedBlog($request)) {
      // ブログ未選択時はブログの新規、編集、削除、一覧、選択以外させない
      $allows = array(
        UsersController::class => array('logout'),
        BlogsController::class => array('index', 'create', 'delete', 'choice'),
        CommonController::class => array('lang', 'install'),
      );
      $controller_name = $request->className;
      $action_name = $request->methodName;
      if (!isset($allows[$controller_name]) || !in_array($action_name, $allows[$controller_name])) {
        $this->setWarnMessage($request, __('Please select a blog'));
        $this->redirect($request, array('controller' => 'Blogs', 'action' => 'index'));
      }
      return;
    }

    // ログイン中でかつブログ選択中の場合ブログ情報を取得し時間設定を行う
    $blog = $this->getBlog($this->getBlogId($request));
    if (is_array($blog) && isset($blog['timezone'])) {
      date_default_timezone_set($blog['timezone']);
    }
  }

  /**
   * ログイン処理
   * @param Request $request
   * @param $user
   * @param null $blog
   */
  protected function loginProcess(Request $request, $user, $blog = null)
  {
    Session::regenerate();

    Session::set($request, 'user_id', $user['id']);
    Session::set($request, 'login_id', $user['login_id']);
    Session::set($request, 'user_type', $user['type']);

    if (!empty($blog)) {
      Session::set($request, 'blog_id', $blog['id']);
      Session::set($request, 'nickname', $blog['nickname']);
    }
  }

  /**
   * ログイン状況
   * @param Request $request
   * @return bool
   */
  protected function isLogin(Request $request): bool
  {
    return !!Session::get($request, 'user_id');
  }

  protected function getInstalledLockFilePath():string
  {
    return Config::get('TEMP_DIR') . "installed.lock";
  }

  protected function isInstalled(): bool{
    $installed_lock_file_path = $this->getInstalledLockFilePath();
    return file_exists($installed_lock_file_path);
  }

  /**
   * ログイン中のIDを取得する
   * @param Request $request
   * @return mixed|null
   */
  protected function getUserId(Request $request)
  {
    return Session::get($request, 'user_id');
  }

  /**
   * ログイン中の名前を取得する
   * @param Request $request
   * @return mixed|null
   */
  protected function getNickname(Request $request)
  {
    return Session::get($request, 'nickname');
  }

  /**
   * ブログIDが設定中かどうか
   * @param Request $request
   * @return bool
   */
  protected function isSelectedBlog(Request $request): bool
  {
    return !!Session::get($request, 'blog_id');
  }

  /**
   * 管理人かどうか
   * @param Request $request
   * @return bool
   */
  protected function isAdmin(Request $request): bool
  {
    return Session::get($request, 'user_type') === Config::get('USER.TYPE.ADMIN');
  }

  /**
   * ブログIDを取得する
   * @param Request $request
   * @return mixed|null
   */
  protected function getBlogId(Request $request)
  {
    return Session::get($request, 'blog_id');
  }

  /**
   * ブログIDを設定する
   * @param Request $request
   * @param null $blog
   */
  protected function setBlog(Request $request, $blog = null)
  {
    if ($blog) {
      Session::set($request, 'nickname', $blog['nickname']);
      Session::set($request, 'blog_id', $blog['id']);
    } else {
      Session::set($request, 'nickname', null);
      Session::set($request, 'blog_id', null);
    }
  }

  /**
   * 情報用メッセージを設定する
   * @param Request $request
   * @param $message
   */
  protected function setInfoMessage(Request $request,$message)
  {
    $this->setMessage($request, $message, 'flash-message-info');
  }

  /**
   * 警告用メッセージを設定する
   * @param Request $request
   * @param $message
   */
  protected function setWarnMessage(Request $request, $message)
  {
    $this->setMessage($request, $message, 'flash-message-warn');
  }

  /**
   * エラー用メッセージを設定する
   * @param Request $request
   * @param $message
   */
  protected function setErrorMessage(Request $request, $message)
  {
    $this->setMessage($request, $message, 'flash-message-error');
  }

  /**
   * メッセージを設定する
   * @param Request $request
   * @param $message
   * @param $type
   */
  protected function setMessage(Request $request, $message, $type)
  {
    $messages = Session::get($request, $type, array());
    $messages[] = $message;
    Session::set($request, $type, $messages);
  }

  /**
   * メッセージ情報を削除し取得する
   * @param Request $request
   * @return array
   */
  protected function removeMessage(Request $request): array
  {
    $messages = array();
    $messages['info'] = Session::remove($request, 'flash-message-info');
    $messages['warn'] = Session::remove($request, 'flash-message-warn');
    $messages['error'] = Session::remove($request, 'flash-message-error');
    return $messages;
  }

  // 存在しないアクションは404へ
  public function __call($name, $arguments): string
  {
    return $this->error404();
  }

  // 404 NotFound Action
  public function error404(): string
  {
    $this->setStatusCode(404);
    return 'admin/common/error404.twig';
  }
}

