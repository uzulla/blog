<header><h2><?php echo __('Blog setting'); ?></h2></header>

<?php $this->display($request, 'BlogSettings/tab.php', array('tab'=>'blog_edit')); ?>

<form method="POST" class="admin-form">

<table>
  <tbody>
    <tr>
      <th><?php echo __('Blog name'); ?></th>
      <td>
        <?php echo \Fc2blog\Web\Html::input($request, 'blog[name]', 'text', array('maxlength'=>255)); ?>
        <?php if (isset($errors['blog']['name'])): ?><?php echo $errors['blog']['name']; ?><?php endif; ?>
      </td>
    </tr>
    <tr>
      <th><?php echo __('Blog Description'); ?></th>
      <td>
        <?php echo \Fc2blog\Web\Html::input($request, 'blog[introduction]', 'text', array('maxlength'=>200)); ?>
        <?php if (isset($errors['blog']['introduction'])): ?><?php echo $errors['blog']['introduction']; ?><?php endif; ?>
      </td>
    </tr>
    <tr>
      <th><?php echo __('Nickname'); ?></th>
      <td>
        <?php echo \Fc2blog\Web\Html::input($request, 'blog[nickname]', 'text', array('maxlength'=>255)); ?>
        <?php if (isset($errors['blog']['nickname'])): ?><p class="error"><?php echo $errors['blog']['nickname']; ?></p><?php endif; ?>
      </td>
    </tr>
    <tr>
      <th><?php echo __('Visibility Blog'); ?></th>
      <td>
        <?php echo \Fc2blog\Web\Html::input($request, 'blog[open_status]', 'select', array('options'=>\Fc2blog\Model\BlogsModel::getOpenStatusList())); ?>
        <?php if (isset($errors['blog']['open_status'])): ?><p class="error"><?php echo $errors['blog']['open_status']; ?></p><?php endif; ?>
      </td>
    </tr>
    <tr>
      <th><?php echo __('View password blog'); ?></th>
      <td>
        <?php echo \Fc2blog\Web\Html::input($request, 'blog[blog_password]', 'text'); ?>
        <?php if (isset($errors['blog']['blog_password'])): ?><?php echo $errors['blog']['blog_password']; ?><?php endif; ?>
      </td>
    </tr>
    <tr>
      <th><?php echo __('Time zone'); ?></th>
      <td>
        <?php echo \Fc2blog\Web\Html::input($request, 'blog[timezone]', 'select', array('options'=>\Fc2blog\Model\BlogsModel::getTimezoneList())); ?>
        <?php if (isset($errors['blog']['timezone'])): ?><p class="error"><?php echo $errors['blog']['timezone']; ?></p><?php endif; ?>
      </td>
    </tr>
    <tr>
      <th><?php echo __('SSL Enable Mode'); ?></th>
      <td>
        <?php echo \Fc2blog\Web\Html::input($request, 'blog[ssl_enable]', 'select', array('options'=>\Fc2blog\Model\BlogsModel::getSSLEnableSettingList())); ?>
        <?php if (isset($errors['blog']['ssl_enable'])): ?><p class="error"><?php echo $errors['blog']['ssl_enable']; ?></p><?php endif; ?>
      </td>
    </tr>
    <tr>
      <th><?php echo __('Redirect status code'); ?></th>
      <td>
        <?php echo \Fc2blog\Web\Html::input($request, 'blog[redirect_status_code]', 'select', array('options'=>\Fc2blog\Model\BlogsModel::getRedirectStatusCodeSettingList())); ?>
        <?php if (isset($errors['blog']['redirect_status_code'])): ?><p class="error"><?php echo $errors['blog']['redirect_status_code']; ?></p><?php endif; ?>
      </td>
    </tr>
    <tr>
      <td class="form-button" colspan="2">
        <input type="submit" value="<?php echo __('Update'); ?>" />
      </td>
    </tr>
  </tbody>
</table>
<input type="hidden" name="sig" value="<?php echo \Fc2blog\Web\Session::get('sig'); ?>">
</form>

