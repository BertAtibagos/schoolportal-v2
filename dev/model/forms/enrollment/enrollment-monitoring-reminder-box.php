
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
<style>
    .msg-cont{
      display: block;
      width: 100%;
      text-align: center;
    }
    .info-msg,
    .success-msg,
    .warning-msg,
    .error-msg {
        margin: 10px 0;
        padding: 10px;
        border-radius: 3px 3px 3px 3px;
        display: none;
    }
    .info-msg {
        color: #059;
        background-color: #BEF;
        display: none;
    }
    .success-msg {
        color: #270;
        background-color: #DFF2BF;
        display: none;
    }
    .warning-msg {
        color: #9F6000;
        background-color: #FEEFB3;
        display: none;
    }
    .error-msg {
        color: #D8000C;
        background-color: #FFBABA;
        display: none;
    }


</style>
<div class="msg-cont">
  <div class="info-msg" id="info-msg">
    <i class="fa fa-info-circle"></i>
  </div>

  <div class="success-msg" id="success-msg">
    <i class="fa fa-check"></i>
  </div>

  <div class="warning-msg" id="warning-msg">
    <i class="fa fa-warning"></i>
  </div>

  <div class="error-msg" id="error-msg">
    <i class="fa fa-times-circle"></i>
  </div>
</div>