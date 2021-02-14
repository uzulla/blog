<?php

namespace Fc2blog\Web\Controller\User;

use Fc2blog\Model\BlogsModel;
use Fc2blog\Web\Controller\AppController;
use Fc2blog\Web\Request;
use Fc2blog\Web\Session;

abstract class UserController extends AppController
{

  /**
   * ブログID取得
   * @param Request $request
   * @return string|null
   */
  public static function getBlogId(Request $request): ?string
  {
    return $request->getBlogId();
  }

  /**
   * 管理画面ログイン中のブログIDを取得する
   * @param Request $request
   * @return string|null
   */
  protected static function getAdminBlogId(Request $request): ?string
  {
    return Session::get($request, 'blog_id');
  }

  /**
   * 管理画面ログイン中のUserIDを取得する
   * @param Request $request
   * @return mixed|null
   */
  protected static function getAdminUserId(Request $request)
  {
    return Session::get($request, 'user_id');
  }

  /**
   * ログイン中のブログかどうかを返却
   * @param Request $request
   * @return bool
   */
  protected function isLoginBlog(Request $request) :bool
  {
    // ログイン中か
    $admin_blog_id = $this->getAdminBlogId($request);
    if (empty($admin_blog_id)) {
      return false;
    }
    // セッションに持っているログイン中のブログIDを取得
    $blog_id = $this->getBlogId($request);
    if ($admin_blog_id == $blog_id) {
      return true;
    }
    // あるいはログイン中のアカウントがブログ所有者か判定
    $blogs_model = new BlogsModel();
    return $blogs_model->isUserHaveBlogId($admin_blog_id, $blog_id);
  }

  /**
   * ブログのパスワードキー
   * @param string $blog_id
   * @return string
   */
  protected static function getBlogPasswordKey(string $blog_id): string
  {
    return 'blog_password.' . $blog_id;
  }

  /**
   * 記事のパスワードキー
   * @param string $blog_id
   * @param int $entry_id
   * @return string
   */
  protected static function getEntryPasswordKey(string $blog_id, int $entry_id): string
  {
    return 'entry_password.' . $blog_id . '.' . (string)$entry_id;
  }
}
