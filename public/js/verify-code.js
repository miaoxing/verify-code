define(function() {
  var oneSecond = 1000;
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
    var that = this;
    var $el = this.$element;

    this.options.btnContent = $el.html();

    $el.click(function () {
      that.disable('发送中...');
      that.send();
    });
  };

  VerifyCode.prototype.send = function () {
    var that = this;

    $.ajax({
      url: that.options.url,
      type: 'post',
      dataType: 'json',
      data: {
        mobile: $(this.options.mobileSelector).val()
      },
      success: function (ret) {
        $.msg(ret);
        if (ret.code > 0) {
          that.countdown();
        } else {
          that.enable();
        }
      }
    });
  };

  VerifyCode.prototype.enable = function (content) {
    this.$element.removeClass('disabled').html(content || this.options.btnContent);
  };

  VerifyCode.prototype.disable = function (content) {
    this.$element.addClass('disabled').html(content);
  };

  VerifyCode.prototype.countdown = function () {
    var that = this;
    var opt = this.options;
    var wait = opt.seconds;
    time();

    function time() {
      if (wait === 0) {
        that.enable('重新发送');
        wait = opt.seconds;
      } else {
        that.disable('重新发送(' + wait + ')');
        wait--;
        timeoutId = setTimeout(function () {
          time();
        }, oneSecond);
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
      var options = $.extend({}, VerifyCode.DEFAULTS, typeof option === 'object' && option);

      if (!data) {
        $this.data('verify-code', (data = new VerifyCode(this, options)));
      }
      if (typeof option === 'string') {
        data[option]();
      } else if (options.show) {
        data.show();
      }
    });
  }

  $.fn.verifyCode = Plugin;
  $.fn.verifyCode.Constructor = VerifyCode;

  return VerifyCode;
});
