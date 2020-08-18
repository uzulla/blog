
<!-- timepicker-addon -->
<?php \Fc2blog\Web\Html::addJS('/js/jquery/jQuery-Timepicker-Addon/src/jquery-ui-timepicker-addon.js'); ?>
<?php if (\Fc2blog\Config::get('LANG')!='en'): ?>
  <?php \Fc2blog\Web\Html::addJS('/js/jquery/jQuery-Timepicker-Addon/src/i18n/jquery-ui-timepicker-' . \Fc2blog\Config::get('LANG') . '.js'); ?>
<?php endif; ?>

<?php \Fc2blog\Web\Html::addJS('/js/js.cookie.js'); ?>

<!-- メディアを追加する -->
<div id="sys-add-media-dialog" title="<?php echo __('Add Media'); ?>">
  <!-- Search Area -->
  <div class="form_area">
    <div class="form_contents">
      <dl class="input_search">
        <dt class="lineform_text_wrap common_input_text"><input type="text" id="sys-add-media-search-keyword" /></dt>
        <dd class="lineform_btn_wrap"><button type="button" id="sys-add-media-search-button" class="lineform_btn touch" /><?php echo __('Search'); ?></button></dd>
      </dl>
    </div>
  </div>
  <hr />

  <!-- Media Area -->
  <div id="sys-add-media-load"></div>
</div>

<?php \Fc2blog\Web\Html::addJS('/js/admin/entry_editor.js'); ?>

<script>
$(function(){
  // form内でEnterしてもsubmitさせない
  common.formEnterNonSubmit('sys-entry-form');

  // 追記と記事の設定の開閉処理
  var cookie_config = {
    expires: <?php echo \Fc2blog\Config::get('COOKIE_EXPIRE'); ?>,
    domain: '<?php echo \Fc2blog\Config::get('COOKIE_DEFAULT_DOMAIN'); ?>',
    path: '/',
    sameSite: 'Lax'
  };
  $('#sys-accordion-extend').on('click', function(){
    var next = $(this).next();
    if (next.is(':visible')) {
      next.slideUp('fast');
      Cookies.set('js_entry_hide_extend', true, cookie_config);
    } else {
      next.slideDown('fast');
      Cookies.remove('js_entry_hide_extend', cookie_config);
    }
  });
  $('#sys-accordion-setting').on('click', function(){
    var next = $(this).next();
    if (next.is(':visible')) {
      next.slideUp('fast');
      Cookies.set('js_entry_hide_setting', true, cookie_config);
    } else {
      next.slideDown('fast');
      Cookies.remove('js_entry_hide_setting', cookie_config);
    }
  });

  // 公開区分による表示処理変更
  $('input[name="entry[open_status]"]').on('change', function(){
    var open_status = $('input[name="entry[open_status]"]:checked').val();
    if (open_status=='<?php echo \Fc2blog\Config::get('ENTRY.OPEN_STATUS.PASSWORD'); ?>') {
      $('.sys-entry-password').slideDown('fast');
    } else {
      $('.sys-entry-password').slideUp('fast');
    }
  });
  $('input[name="entry[open_status]"]:checked').change();

  // date time picker
  $('.date-time-picker').datetimepicker({
    dateFormat: 'yy-mm-dd',
    timeFormat: 'HH:mm:ss'
  });

  // ユーザータグ
  // タグ追加(登録済みは登録しない)
  function addUserTag(tag){
    tag = jQuery.trim(tag);
    if (tag=="") {
      return ;
    }
    var tags = getTags();
    for (var i = 0; i < tags.length; i++) {
      if (tags[i]==tag) {
        return ;
      }
    }
    tag = tag.replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    var html = $('<li><span class="ui-icon ui-icon-circle-close"></span><input type="hidden" name="entry_tags[]" value="' + tag + '" />' + tag + '</li>');
    html.on('click', function(){
      $(this).closest('li').remove();
    });
    $('#sys-add-tags').append(html);
  }
  // タグ一覧をテキストの配列で取得
  function getTags(){
    var tags = [];
    $('#sys-add-tags > li > input').each(function(){
      tags.push($(this).val());
    });
    return tags;
  }

  // タグの追加イベント
  $('#sys-add-tag-text').on('keypress', function(e){
    if ((e.which && e.which===13) || (e.keyCode && e.keyCode===13)) {
      $('#sys-add-tag-button').click();
    }
  });
  $('#sys-add-tag-button').on('click', function(){
    var tags = $('#sys-add-tag-text').val().split(',');
    for (var i = 0; i < tags.length; i++) {
      addUserTag(tags[i]);
    }
    $('#sys-add-tag-text').val('');
    return false;
  });
  $('#sys-use-well-tags > li').on('click', function(){
    addUserTag($(this).html());
  });

  // タグの初期設定(編集用)
  <?php $tags = $request->get('entry_tags', array()); ?>
  <?php foreach ($tags as $tag) : ?>
    addUserTag("<?php echo str_replace('"', '\"',$tag); ?>");
  <?php endforeach; ?>

  // プレビュー処理を行う
  $('#sys-entry-form-preview').click(function(){
    var action = '//<?php echo \Fc2blog\Config::get('DOMAIN_USER'); ?>';
    action    += '<?php echo \Fc2blog\App::userURL($request,array('controller'=>'Entries', 'action'=>'preview', 'blog_id'=>$this->getBlogId($request))); ?>';
    $('#sys-entry-form').prop('action', action);
    $('#sys-entry-form').prop('target', '_preview');
    $('#sys-entry-form').submit();
  });

  // submit処理を行う
  $('#sys-entry-form-submit').click(function(){
    var action = '';
    if ($('#sys-entry-form').find('input[name=id]').val()) {
      action = '<?php echo \Fc2blog\Web\Html::url($request, array('controller'=>'Entries', 'action'=>'edit')); ?>';
    } else {
      action = '<?php echo \Fc2blog\Web\Html::url($request, array('controller'=>'Entries', 'action'=>'create')); ?>';
    }
    $('#sys-entry-form').prop('action', action);
    $('#sys-entry-form').prop('target', '_self');
  });

  // メディア追加ボタンを追加する
  function addEditorMenu(key){
    // メニュー作成
    var html = '';
    html    += '<ul class="add-editor-menu">';
    html    += '<li><input type="button" value="<?php echo __('Add Media'); ?>" id="sys-add-image-' + key + '" /></li>';
    html    += '</ul>';
    $('#sys-entry-' + key).before(html);

    // 追加したメニューにイベント追加
    $('#sys-add-image-' + key).click(function(){
      addMedia.open(key, {
        Add: '<?php echo __('Add'); ?>',
        elrte: false
      });
    });
  }

  addEditorMenu('body');
  addEditorMenu('extend');
});
</script>

