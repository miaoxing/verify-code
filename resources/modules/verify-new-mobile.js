export default function verifyNewMobile(mobileVerified, $container, $mobile) {
  if (!mobileVerified) {
    return;
  }

  if (!$container) {
    $container = $('.js-verify-code-from-group');
  }
  if (!$mobile) {
    $mobile = $('.js-mobile');
  }

  var mobile = $mobile.val();
  if (mobile) {
    $container.hide();
  }

  $mobile.change(function () {
    if ($mobile.val() === mobile) {
      $container.hide();
    } else {
      $container.show();
    }
  });
}
