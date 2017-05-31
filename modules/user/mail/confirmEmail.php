<?php
/**
 * @package   yii2-user
 * @author    Yuri Shekhovtsov <shekhovtsovy@yandex.ru>
 * @copyright Copyright &copy; Yuri Shekhovtsov, lowbase.ru, 2015 - 2016
 * @version   1.0.0
 */

/* @var $this yii\web\View */
/* @var $model \lowbase\user\models\User */

use yii\helpers\Html;

if (isset($model) && $model) {
    $confirmLink = Yii::$app->urlManager->createAbsoluteUrl(['confirm', 'token' => $model->email_confirm_token]);
?>

<style id="builder-styles">body {
        margin: 0;
        padding: 0;
    }
    body, table, td, p, a, li {
        -webkit-text-size-adjust: 100%;
        -ms-text-size-adjust: 100%;
    }
    a {
        word-wrap: break-word;
    }
    table td {
        border-collapse: collapse;
    }
    table {
        border-spacing: 0;
        border-collapse: collapse;
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
    }
    table, td {
        mso-table-lspace: 0pt;
        mso-table-rspace: 0pt;
    }
    @media only screen and (max-width: 640px) {
        table[class="main"],td[class="main"] { width:100% !important; min-width: 200px !important; }
        table[class="logo-img"] { width:100% !important; float: none; margin-bottom: 15px;}
        table[class="logo-img"] td { text-align: center !important;}
        table[class="logo-title"] { width:100% !important; float: none;}
        table[class="logo-title"] td { text-align: center; height: auto}
        table[class="logo-title"] h1 { font-size: 24px !important; }
        table[class="logo-title"] h2 { font-size: 18px !important; }
        td[class="header-img"] img { width:100% !important; height:auto !important; }
        td[class="title"] { padding-left: 15px !important; padding-right: 15px !important; }
        td[class="title"] h1 { font-size: 24px !important; }
        td[class="block-text"] { padding-left: 15px !important; padding-right: 15px !important; }
        td[class="block-text"] h2 { font-size: 20px !important; line-height: 170% !important; }
        td[class="block-text"] p { font-size: 16px !important; line-height: 170% !important; }
        td[class="block-text"] li { font-size: 16px !important; line-height: 170% !important; }
        td[class="two-columns"] { padding-left: 15px !important; padding-right: 15px !important; }
        table[class="text-column"] { width:100% !important; float: none; margin-bottom: 15px;}
        td[class="image-caption"] { padding-left: 15px !important; padding-right: 15px !important; }
        table[class="image-caption-container"] { width:100% !important;}
        table[class="image-caption-column"] { width:100% !important; float: none;}
        td[class="image-caption-content"] img { width:100% !important; height:auto !important; }
        td[class="image-caption-content"] h2 { font-size: 20px !important; line-height: 170% !important; }
        td[class="image-caption-content"] p { font-size: 16px !important; line-height: 170% !important; }
        td[class="image-caption-top-gap"] { height: 15px !important; }
        td[class="image-caption-bottom-gap"] { height: 5px !important; }
        td[class="text"] { width:100% !important; }
        td[class="text"] p { font-size: 16px !important; line-height: 170% !important; }
        td[class="text"] h2 { font-size: 20px !important; line-height: 170% !important; }
        td[class="gap"] { display:none; }
        td[class="header"] { padding: 25px 25px 25px 25px !important; }
        td[class="header"] h1 { font-size: 24px !important; }
        td[class="header"] h2 { font-size: 20px !important; }
        td[class="footer"] { padding-left: 15px !important; padding-right: 15px !important; }
        td[class="footer"] p { font-size: 13px !important; }
        table[class="footer-side"] { width: 100% !important; float: none !important; }
        td[class="footer-side"] { text-align: center !important; }
        td[class="social-links"] { text-align: center !important; }
        table[class="footer-social-icons"] { float: none !important; margin: 0px auto !important; }
        td[class="social-icon-link"] { padding: 0px 5px !important; }
        td[class="image"] img { width:100% !important; height:auto !important; }
        td[class="image"] { padding-left: 15px !important; padding-right: 15px !important; }
        td[class="image-full"] img { width:100% !important; height:auto !important; }
        td[class="image-full"] { padding-left: 0px !important; padding-right: 0px !important; }
        td[class="image-group"] img { width:100% !important; height:auto !important; margin: 15px 0px 15px 0px !important; }
        td[class="image-group"] { padding-left: 15px !important; padding-right: 15px !important; }
        table[class="image-in-table"] { width:100% !important; float: none; margin-bottom: 15px;}
        table[class="image-in-table"] td { width:100% !important;}
        table[class="image-in-table"] img { width:100% !important; height:auto !important; }
        td[class="image-text"] { padding-left: 15px !important; padding-right: 15px !important; }
        td[class="image-text"] p { font-size: 16px !important; line-height: 170% !important; }
        td[class="image-text"] > table { width: 100%!important }
        td[class="divider-simple"] { padding-left: 15px !important; padding-right: 15px !important; }
        td[class="divider-full"] { padding-left: 0px !important; padding-right: 0px !important; }
        td[class="social"] { padding-left: 15px !important; padding-right: 15px !important; }
        table[class="preheader"] { display:none; }
        td[class="preheader-gap"] { display:none; }
        td[class="preheader-link"] { display:none; }
        td[class="preheader-text"] { width:100%; }
        td[class="buttons"] { padding-left: 15px !important; padding-right: 15px !important; }
        table[class="button"] { width:100% !important; float: none; }
        td[class="content-buttons"] { padding-left: 15px !important; padding-right: 15px !important; }
        td[class="buttons-full-width"] { padding-left: 0px !important; padding-right: 0px !important; }
        td[class="buttons-full-width"] a { width:100% !important; padding-left: 0px !important; padding-right: 0px !important; border-radius: 0!important; }
        td[class="buttons-full-width"] span { width:100% !important; padding-left: 0px !important; padding-right: 0px !important; }
        table[class="content"] { width:100% !important; float: none !important;}
        td[class="gallery-image"] { width:100% !important; padding: 0px !important;}
        table[class="social"] { width: 100%!important; text-align: center!important; }
        table[class="links"] { width: 100%!important; }
        table[class="links"] td { text-align: center!important; }
        table[class="footer-btn"] { text-align: center!important; width: 100%!important; margin-bottom: 10px; }
        table[class="footer-btn-wrap"] { margin-bottom: 0px; width: 100%!important; }
        td[class="head-social"]  { width: 100%!important; text-align: center!important; padding-top: 20px; }
        td[class="head-logo"]  { width: 100%!important; text-align: center!important; }
        tr[class="header-nav"] { display: none; }
    }</style><body style="background: rgb(204, 204, 204); padding: 50px 5px;"><table width="640" cellspacing="0" cellpadding="0" border="0" align="center" data-type="image" class="main" style="display: table; background-color: rgb(255, 255, 255);"><tbody><tr><td align="left" class="image" style="padding: 15px 50px;"><img border="0" src="http://mailtousa.com/img/mailtousa.png" tabindex="0" style="display: block; width: 240px;height:32px;"></td></tr></tbody></table>    <table width="640" cellspacing="0" cellpadding="0" border="0" align="center" data-type="divider" class="main" style="border: 0px; display: table; background-color: rgb(255, 255, 255);"><tbody><tr><td class="divider-simple" style="padding: 2px 50px 0px;"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 1px solid rgb(218, 223, 225);"><tbody><tr><td width="100%" height="15px"></td></tr></tbody></table></td></tr></tbody></table>    <table width="640" cellspacing="0" cellpadding="0" border="0" align="center" data-type="text-block" class="main" style="display: table; background-color: rgb(255, 255, 255);"><tbody><tr><td data-block-id="background" align="left" class="block-text" style="font-size: 13px; color: rgb(0, 0, 0); line-height: 22px; padding: 10px 50px 2px;font-family: Arial, serif;"><p style="margin: 0px 0px 10px; line-height: 22px; font-size: 13px; text-align: left;" data-block-id="text-area" data-mce-style="margin: 0px 0px 10px; line-height: 22px; font-size: 13px; text-align: left;"><span style="font-size: 14pt;" data-mce-style="font-size: 14pt;">Hello !</span></p><p style="margin: 0px 0px 10px; line-height: 22px; font-size: 13px; text-align: left;" data-block-id="text-area" data-mce-style="margin: 0px 0px 10px; line-height: 22px; font-size: 13px; text-align: left;"><span style="font-size: 12pt;" data-mce-style="font-size: 12pt;">To confirm the address and primary login click button</span></p></td></tr></tbody></table>    <table width="640" cellspacing="0" cellpadding="0" border="0" bgcolor="#FFFFFF" align="center" data-type="button" class="main" style="display: table; background-color: rgb(255, 255, 255);"><tbody><tr><td class="buttons-full-width" style="padding: 2px 50px 15px;"><table cellspacing="0" cellpadding="0" border="0" align="center" class="button"><tbody><tr><td class="button" style="margin: 10px;"><a href="<?=$confirmLink;?>" data-default="1" class="button-1" style="color: rgb(255, 255, 255); font-family: Arial, serif; font-size: 15px; line-height: 21px; border-radius: 0px; text-align: center; text-decoration: none; font-weight: bold; display: block; margin: 0px; padding: 12px 20px; background-color: rgb(0, 106, 193);">CONFIRM DATA ACCOUNT</a></td></tr></tbody></table></td></tr></tbody></table>    <table width="640" cellspacing="0" cellpadding="0" border="0" align="center" data-type="divider" class="main" style="border: 0px; display: table; background-color: rgb(255, 255, 255);"><tbody><tr><td class="divider-simple" style="padding: 2px 50px;"><table width="100%" cellspacing="0" cellpadding="0" border="0" style="border-top: 1px solid rgb(218, 223, 225);"><tbody><tr><td width="100%" height="15px"></td></tr></tbody></table></td></tr></tbody></table>    <table width="640" cellspacing="0" cellpadding="0" border="0" align="center" data-type="text-block" class="main" style="display: table; background-color: rgb(255, 255, 255);"><tbody><tr><td data-block-id="background" align="left" class="block-text" style="font-size: 13px; color: rgb(0, 0, 0); line-height: 22px; padding: 2px 50px 10px;font-family: Arial, serif;"><p style="margin: 0px 0px 10px; line-height: 22px; font-size: 13px; text-align: center;" data-block-id="text-area" data-mce-style="margin: 0px 0px 10px; line-height: 22px; font-size: 13px; text-align: center;"><span style="font-size: 12pt;" data-mce-style="font-size: 12pt;">If you have not registered on our site, then simply delete this email</span></p></td></tr></tbody></table>    <table width="640" cellspacing="0" cellpadding="0" border="0" align="center" data-type="text-block" class="main" style="display: table; background-color: rgb(238, 238, 238);"><tbody><tr><td data-block-id="background" align="left" style="font-family: Arial, serif; font-size: 13px; color: rgb(0, 0, 0); line-height: 22px; padding: 2px 50px;"><p style="text-align: center;" data-mce-style="text-align: center;">MailToUsa.com</p></td></tr></tbody></table>

<?php
}
?>
