<style type="text/css">
   .td{
      text-align: center;
   }
   .tr{
      border: 1px solid black;
   }
   .table {
     border-collapse: collapse;
   }
   .thead{
      width: 40%;
      text-align: center;
   }
</style>
<table  border="0" cellspacing="0" cellpadding="0" style="width:800px!important">
   <tbody>
      <tr>
         <td align="center">
            <table style="border:1px solid #eaeaea;border-radius:5px;margin:40px 0; width: 800px"  border="0" cellspacing="0" cellpadding="40">
               <tbody>
                  <tr>
                     <td align="center">
                        <div style="font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen,Ubuntu,Cantarell,Fira Sans,Droid Sans,Helvetica Neue,sans-serif;text-align:left;width:800px">
                           <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%!important">
                              <tbody>
                                 <tr>
                                    <td align="center">
                                       <div><img src="http://medicalvipec.com/assets/img/logo.png" ></div>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                           <p style="color:  #939598;">
                              Email:{{$user->email}} <br>
                              Teléfono: {{$user->telefono}}
                           </p>


                           <h1 style="font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen,Ubuntu,Cantarell,Fira Sans,Droid Sans,Helvetica Neue,sans-serif;font-size:20px;font-weight:bold;margin:0px 0;padding:0; text-align: center">
                                       DETALLES DE LA ENTREGA
                           </h1>
                           <br>


                           <table width="100%" style="color: #939598">
                              <tbody>
                                 <tr>
                                    <td>
                                       Local: test
                                    </td>
                                 </tr>
                                 <tr>
                                    <td>
                                       Lugar de Entrega:
                                    </td>
                                 </tr>
                                 <tr >
                                    <td style="border: 1px solid #939598; border-radius: 5px; padding: 5px">
                                       <div >
                                          TEST
                                       </div>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                           <br>
                           <h1 style="font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen,Ubuntu,Cantarell,Fira Sans,Droid Sans,Helvetica Neue,sans-serif;font-size:20px;font-weight:bold;margin:0px 0;padding:0; text-align: center">
                                       DETALLES DE LA ORDEN
                           </h1>
                           <br>
                           
                           <table width="100%">
                              <tbody>
                                 <tr align="center">
                                    <td>Método de Pago: Pago en línea</td>
                                    <td>Fecha de la Orden: {{$fecha}}</td>
                                 </tr>

                              </tbody>
                           </table>
                           <br>

                           <h1 style="font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Oxygen,Ubuntu,Cantarell,Fira Sans,Droid Sans,Helvetica Neue,sans-serif;font-size:20px;font-weight:bold;margin:0px 0;padding:0; text-align: center">
                                       DETALLES DEl PEDIDO
                           </h1>
                           <br>

                           <table class="table" width="100%" style="border: 1px solid black">
                              <thead>
                                 <td class="thead">Cantidad</td>
                                 <td class="thead">Producto</td>
                                 <td >Precio</td>
                              </thead>
                              <tbody >
                                 @foreach($solicitud->examenes as $examen)
                                 <tr class="tr">
                                    <td class="td">1</td>
                                    <td class="td">{{$examen->examen->nombre}}</td>
                                    <td style="padding-left: 2px">$ {{$examen->examen->precio}}</td>
                                 </tr>
                                 @endforeach
                                 <tr class="tr">
                                    <td colspan="2" align="right">Subtotal:</td>
                                    <td style="padding-left: 2px"> $ {{$solicitud->precio}} </td>
                                 </tr>
                                 <tr class="tr">
                                    <td colspan="2" align="right">Impuesto:</td>
                                    <td style="padding-left: 2px"> $ {{ $solicitud->total - $solicitud->precio }} </td>
                                 </tr>

                                 <tr class="tr">
                                    <td colspan="2" align="right">Total:</td>
                                    <td style="padding-left: 2px"> $ {{$solicitud->total}}</td>
                                 </tr>
                              </tbody>
                           </table>
                           <br>

                           <p >
                              Forma de Pago: {{$request['card_type']}} <br>
                              Código de Transacción: {{$request['id_transaccion']}}   <br>
                              Código de Autorización: {{$request['authorization_code']}} 
                           </p>

                        </div>
                     </td>
                  </tr>
               </tbody>
            </table>
         </td>
      </tr>
   </tbody>
</table