用于发送手机验证码


### 前台使用方法

1. 显示"发送验证码"的按钮.js-verify-code-send
2. 加载js模块plugins/verify-code/js/verify-code
3. 调用jQuery方法 $('.js-verify-code-send').verifyCode();
4. (可选)配置url为校验数据并发送验证码的接口

```html
<form class="form" method="post">
  <div class="form-group">
    <label for="mobile" class="control-label">手机号码</label>
    <div class="col-control">
      <input type="tel" class="js-mobile form-control" id="mobile" name="mobile" placeholder="请输入手机号码" value="">
    </div>
  </div>
  <div class="form-group">
    <label for="verifyCode" class="control-label">验证码</label>
    <div class="col-control">
      <div class="input-group">
        <input type="tel" class="form-control" id="verifyCode" name="verifyCode">
        <span class="input-group-btn border-left">
          <button class="js-verify-code-send text-primary btn btn-secondary form-link" type="button">发送验证码</button>
        </span>
      </div>
    </div>
  </div>
  <div class="form-footer">
    <button type="button" class="btn btn-primary btn-block">提交</button>
  </div>
</form>

<?= $block->js() ?>
<script>
  require(['plugins/verify-code/js/verify-code'], function () {
    $('.js-verify-code-send').verifyCode({
      url: $.url('users/send-register-verify-code')
    });
  });
</script>
<?= $block->end() ?>
```

#### $.fn.verifyCode的选项

名称                | 类型    | 默认值                       | 说明
--------------------|---------|------------------------------|------
seconds             | int     | 60                           | 倒计时时间,需和后台保持一致
url                 | string  | $.url('verify-codes/create') | 获取验证码的地址
mobileSelector      | string  | .js-mobile                   | 获取手机号码的选择器

### 后台使用方法

#### 1. 发送验证码

```php
// 1. 检查手机号是否不存在(注册) 或 检查手机号是否存在(忘记密码)
// xxx

// 2. 调用接口发送短信验证码
$ret = wei()->verifyCode->send($req['mobile']);
```

#### 2. 检查验证码是否正确

注意需传入手机号码,防止更换了号码

```php
$ret = wei()->verifyCode->check($req['mobile'], $req['code']);
```
