<!DOCTYPE html>
<html>
<head>
  <title>Example Add Card</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>

  <link href="https://cdn.paymentez.com/ccapi/sdk/payment_stable.min.css" rel="stylesheet" type="text/css"/>
  <script src="https://cdn.paymentez.com/ccapi/sdk/payment_stable.min.js" charset="UTF-8"></script>

  <style>
    .panel {
      margin: 0 auto;
      background-color: #F5F5F7;
      border: 1px solid #ddd;
      padding: 20px;
      display: block;
      width: 80%;
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
    }

    .btn {
      background: rgb(140, 197, 65); /* Old browsers */
      background: -moz-linear-gradient(top, rgba(140, 197, 65, 1) 0%, rgba(20, 167, 81, 1) 100%); /* FF3.6-15 */
      background: -webkit-linear-gradient(top, rgba(140, 197, 65, 1) 0%, rgba(20, 167, 81, 1) 100%); /* Chrome10-25,Safari5.1-6 */
      background: linear-gradient(to bottom, rgba(140, 197, 65, 1) 0%, rgba(20, 167, 81, 1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
      filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#44afe7', endColorstr='#3198df', GradientType=0);
      color: #fff;
      display: block;
      width: 100%;
      border: 1px solid rgba(46, 86, 153, 0.0980392);
      border-bottom-color: rgba(46, 86, 153, 0.4);
      border-top: 0;
      border-radius: 4px;
      font-size: 17px;
      text-shadow: rgba(46, 86, 153, 0.298039) 0px -1px 0px;
      line-height: 34px;
      -webkit-font-smoothing: antialiased;
      font-weight: bold;
      display: block;
      margin-top: 20px;
    }

    .btn:hover {
      cursor: pointer;
    }
  </style>

</head>
<body>
<div class="panel">
  <form id="add-card-form">
    <input type="hidden" name="uid" value="<?php echo $_GET["id"]; ?>" id="uid">
    <input type="hidden" name="uemail" value="<?php echo $_GET["email"]; ?>" id="uemail">
    <div class="payment-form" id="my-card" data-capture-name="true"></div>
    <button class="btn">Save</button>
    <br/>
    <div id="messages"></div>
  </form>

</div>
</body>

<script>
  $(function () {

    //
    // EXAMPLE CODE FOR INTEGRATION
    // ---------------------------------------------------------------------------
    //
    //  1.) You need to import the JS file -> https://cdn.paymentez.com/ccapi/sdk/payment_stable.min.js
    //
    //  2.) You need to import the CSS file -> https://cdn.paymentez.com/ccapi/sdk/payment_stable.min.css
    //
    //  3.) Add the Payment Form
    //  <div class="payment-form" id="my-card" data-capture-name="true"></div>
    //
    //  3.) Init library
    //  Replace "PAYMENT_CLIENT_APP_CODE" and "PAYMENT_CLIENT_APP_KEY" with your own Client Credentials.
    //
    // 4.) Add Card: converts sensitive card data to a single-use token which you can safely pass to your server to charge the user.

    /**
     * Init library
     *
     * @param env_mode `prod`, `stg`, `local` to change environment. Default is `stg`
     * @param payment_client_app_code provided by Paymentez.
     * @param payment_client_app_key provided by Paymentez.
     */
    Payment.init('stg', 'TPP2-EC-CLIENT', 'sDAwQAdBqetYhVZFFLpOg6FU2cmjF0');

    let form = $("#add-card-form");
    let submitButton = form.find("button");
    let submitInitialText = submitButton.text();

    $("#add-card-form").submit(function (e) {
      let myCard = $('#my-card');
      $('#messages').text("");
      let cardToSave = myCard.PaymentForm('card');
      if (cardToSave == null) {
        $('#messages').text("Invalid Card Data");
      } else {
        submitButton.attr("disabled", "disabled").text("Card Processing...");

        /*
        After passing all the validations cardToSave should have the following structure:

         let cardToSave = {
                            "card": {
                              "number": "5119159076977991",
                              "holder_name": "Martin Mucito",
                              "expiry_month": 9,
                              "expiry_year": 2020,
                              "cvc": "123",
                              "type": "vi"
                            }
                          };

        */


        let uid = $('#uid').val();
        let email = $('#uemail').val();

        /* Add Card converts sensitive card data to a single-use token which you can safely pass to your server to charge the user.
         *
         * @param uid User identifier. This is the identifier you use inside your application; you will receive it in notifications.
         * @param email Email of the user initiating the purchase. Format: Valid e-mail format.
         * @param card the Card used to create this payment token
         * @param success_callback a callback to receive the token
         * @param failure_callback a callback to receive an error
         */
        Payment.addCard(uid, email, cardToSave, successHandler, errorHandler);

      }

      e.preventDefault();
    });


    let successHandler = function (cardResponse) {
      console.log(cardResponse.card);
      if (cardResponse.card.status === 'valid') {
        valid
        $('#messages').html('<div align="center"><img src="bien.png"><br><br>Su pago fue realizado correctamente</div>');
        history.pushState(null, "", "http://localhost/add-card/index.php?response=success");
      } else if (cardResponse.card.status === 'review') {
        $('#messages').html('<div align="center"><img src="mal.png"><br><br>Error de Tarjeta contactacte a su banco</div>');
        history.pushState(null, "", "http://localhost/pago-medical/index.php?response=error");
      } else {
        error
        $('#messages').html('<div align="center"><img src="mal.png"><br><br>Error de Tarjeta contactacte a su banco</div>');
        history.pushState(null, "", "http://localhost/pago-medical/index.php?response=error");
      }
      submitButton.removeAttr("disabled");
      submitButton.text(submitInitialText);
    };

    let errorHandler = function (err) {
      console.log(err.error);
      $('#messages').html(err.error.type);
      submitButton.removeAttr("disabled");
      submitButton.text(submitInitialText);
    };

  });
</script>
</html>