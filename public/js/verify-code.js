define(function() {
  var timeoutId;

  var VerifyCode = function(element, options) {
    this.options = options;
    this.$element = $(element);

    this.init();
  };

  VerifyCode.DEFAULTS = {
    seconds: 60,
    url: $.url('verify-codes/create'),
    mobileSelector: '.js-mobile',
    btnContent: ''
  };

  VerifyCode.prototype.init = function () {
    var self = this;
    var $el = this.$element;

    this.options.btnContent = $el.html();

    $el.click(function () {
      self.disable('发送中...');
      self.send();
    });
  };

  VerifyCode.prototype.send = function () {
    var self = this;

    $.ajax({
      url: self.options.url,
      type: 'post',
      dataType: 'json',
      data: {
        mobile: $(this.options.mobileSelector).val()
      },
      success: function (ret) {
        $.msg(ret);
        if (ret.code > 0) {
          self.countdown();
        } else {
          self.enable();
        }
      }
    })
  };

  VerifyCode.prototype.enable = function (content) {
    this.$element.removeClass('disabled').html(content || this.options.btnContent);
  };

  VerifyCode.prototype.disable = function (content) {
    this.$element.addClass('disabled').html(content);
  };

  VerifyCode.prototype.countdown = function () {
    var self = this;
    var opt = this.options;
    var wait = opt.seconds;
    time();

    function time() {
      if (wait == 0) {
        self.enable('重新发送');
        wait = opt.seconds;
      } else {
        self.disable('重新发送(' + wait + ')');
        wait--;
        timeoutId = setTimeout(function () {
          time()
        }, 1000);
      }
    }
  };

  /**
   * 清除倒计时状态,并启用按钮
   */
  VerifyCode.prototype.reset = function () {
    if (timeoutId) {
      clearTimeout(timeoutId);
    }
    this.enable();
  };

  function Plugin(option) {
    return this.each(function () {
      var $this   = $(this);
      var data    = $this.data('verify-code');
      var options = $.extend({}, VerifyCode.DEFAULTS, typeof option == 'object' && option);

      if (!data) $this.data('verify-code', (data = new VerifyCode(this, options)));
      if (typeof option == 'string') data[option]();
      else if (options.show) data.show();
    });
  }

  $.fn.verifyCode = Plugin;
  $.fn.verifyCode.Constructor = VerifyCode;

  return VerifyCode;
});
