<style>
       @import url(https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.css);
    </style>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%!important">
       <tbody>
          <tr>
             <td align="center">
                <table style="border:1px solid #eaeaea;border-radius:5px;margin:40px 0" width="600" border="0" cellspacing="0" cellpadding="0">
                   <tbody>
                      @if($orden->pago->tipo == 2)
                      <tr >
                        <td align="center" >
                          <p class="text-danger">
                            Luego de realizar la transferencia envíe su comprobante a nuestro email info@laretrateriaec.com
                            <h5 class="mt-3 title">BANCO PICHINCHA</h5>
                                      <p class="text-muted mb-0">
                                          Cuenta de Ahorros<br>
                                          2205782640<br>
                                          Sofía Becerra<br>
                                          0104388244<br>
                                          info@laretrateriaec.com <br>
                                          099-4625268<br>
                                      </p>
                          </p>
                        </td>
                      </tr>
                      @endif
                      <tr>
                         <td align="center">
                                <p style="font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#000;font-weight:bold;text-align:center;">¡Gracias por confiar en nosotros!</p>
                                <div style="padding-top: 40px; padding-bottom: 40px;"><img src="https://laretrateriaec.com/assets/img/logo.png"></div>
                                <p style="font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#000;font-weight:normal;text-align:center;">Queremos dejar en ti la mejor impresión y asegurarte que tus recuerdos llegarán a satisfacer todas tus expectativas. 
                                    <br>
                                    ¡Ya nos pusimos manos a la obra para que dentro de poco tus recuerdos lleguen a su destino!<br><br><b>Tu número de pedido es {{$orden->numero_orden}} y la fecha de entrega aproximada es (72 horas después de realizado del pedido)</p>
                                <table width="100%" border="0" cellspacing="0" cellpadding="10" style="width:100%!important">
                  <tr>
                    <td width="50%">
                      @if($orden->pago->tipo == 1)
                      <p style="padding-right:30px;font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#000;font-weight:normal;text-align:left;">
                                     <b>C&oacute;digo de Transacci&oacute;n:</b> {{$orden->pago->transaction_reference}}
                                  </p>
                      <p style="padding-right:30px;font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#000;font-weight:normal;text-align:left;">
                                     <b>C&oacute;digo de Autorizaci&oacute;n:</b> {{$orden->pago->authorization_code}}
                                  </p>
                                  @endif
                    </td>
                    <td width="50%">
                      <p style="padding-right:30px;font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#000;font-weight:normal;text-align:right;">
                                     <b>Fecha:</b> {{$orden->pago->fecha}}
                                  </p>
                    </td>

                  </tr>
                </table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%!important">
                               <tbody>
                                  <tr>
                                     <td align="center" bgcolor="#f6f6f6" valign="middle" height="40" style="font-family:Arial, Helvetica, sans-serif;font-size:16px;font-weight:bold">Datos de la factura</td>
                                  </tr>
                               </tbody>
                            </table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="20" >
                               <tr>
                                  <td>
                                     <p style="font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#000;font-weight:normal;text-align:justify;">
                                        <b>Nombre o Raz&oacute;n Social:</b> {{$orden->pago->nombres}}<br>
                                        <b>CI/RUC:</b> {{$orden->pago->documento}}<br>
                                        <b>Direccci&oacute;n:</b> {{$orden->pago->direccion}}<br>
                                        <b>Tel&eacute;fono:</b> {{$orden->pago->telefono}}<br>
                                        <b>Email:</b> {{$orden->pago->email}}<br>
                                     </p>
                                  </td>
                               </tr>
                            </table>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%!important">
                               <tbody>
                                  <tr>
                                     <td align="center" bgcolor="#f6f6f6" valign="middle" height="40" style="font-family:Arial, Helvetica, sans-serif;font-size:16px;font-weight:bold">Detalles</td>
                                  </tr>
                               </tbody>
                            </table>
                            <table width="100%" style="font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#000;font-weight:normal;border:1px solid #eaeaea;" border="0" cellspacing="0" cellpadding="5">
                               <thead>
                                  <tr>
                                     <th style="min-width: 200px;border:1px solid #eaeaea;">Producto</th>
                                     <th style="min-width: 50px;border:1px solid #eaeaea;">Precio</th>
                                     <th style="min-width: 50px;border:1px solid #eaeaea;">Cantidad</th>
                                     <th style="min-width: 50px;border:1px solid #eaeaea;">Total</th>
                                  </tr>
                               </thead>
                               <tbody>

                                @foreach( $orden->productos as $producto )
                                @if(count($producto->fotos) > 0 && $producto->estatus == 1)
                  <tr>
                    <td style="border:1px solid #eaeaea;">
                    {{$producto->producto->nombre}}
                    </td>
                    <?php
                                          $cantidad = 0;
                                        ?>
                                        @foreach($producto->fotos as $foto)
                                        <?php
                                          $cantidad = $cantidad + $foto->cantidad;
                                        ?>
                                        @endforeach
                    <td style="border:1px solid #eaeaea;text-align:center;">
                    {{round($producto->total / $cantidad, 2)}}
                    </td>
                    <td style="border:1px solid #eaeaea;text-align:center;">
                    {{$cantidad}}
                    </td>
                    <td style="border:1px solid #eaeaea;text-align:center;">
                    {{$producto->total}}
                    </td>
                  </tr>
                  @endif
                  @endforeach
                  <tr>
                    <td></td><td></td>
                    <td style="border:1px solid #eaeaea;text-align:right;"><b>Subtotal<b></td>
                    <td  style="border:1px solid #eaeaea;text-align:center;">{{$orden->sub_total}}</td>
                  </tr>

                  <tr>
                    <td></td><td></td>
                    <td  style="border:1px solid #eaeaea;text-align:right;"><b>Envío<b></td>
                    <td  style="border:1px solid #eaeaea;text-align:center;">{{$orden->costo_envio}}</td>
                  </tr>

                  <tr>
                    <td></td><td></td>
                    <td  style="border:1px solid #eaeaea;text-align:right;"><b>Iva<b></td>
                    <td  style="border:1px solid #eaeaea;text-align:center;">{{$orden->iva}}</td>
                  </tr>

                  <tr>
                    <td></td><td></td>
                    <td   style="border:1px solid #eaeaea;text-align:right;"><b>Total<b></td>
                    <td  style="border:1px solid #eaeaea;text-align:center;">{{$orden->total}}</td>
                  </tr>


                               </tbody>
                            </table>

                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%!important">
                                        <tbody>
                                           <tr>
                                              <td align="center" bgcolor="#f6f6f6" valign="middle" height="40" style="font-family:Arial, Helvetica, sans-serif;font-size:16px;font-weight:bold">Datos Env&iacute;o</td>
                                           </tr>
                                        </tbody>
                                     </table>
                     <p style="font-family:Arial, Helvetica, sans-serif; font-size:11px;color:#000;font-weight:normal;text-align:center;">
                      @if( $orden->direccion->forma_entrega == null)
                      <b>Empresa de Env&iacute;o:</b>
                        @if( $orden->guia != null )
                          RapidService <br>
                          <b>C&oacute;digo de Rastreo: </b> {{$orden->guia->qr}}
                          <br>
                          <a target="_blank" href="{{$orden->guia->rastreo}}">{{$orden->guia->rastreo}}</a>
                        @else
                          ServiEntrega
                          <br>
                        @endif
                      @else 
                        <b>Recogida en Oficina</b>
                      @endif
                     </p>
                     <p style="padding:0 20px 20px 20px;font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#000;font-weight:normal;text-align:justify;">
                                        <b>Nombre y Apellidos:</b> {{$orden->direccion->nombres}}<br><br>
                                        <b>CI/RUC:</b> {{$orden->direccion->cedula}}<br>
                      <b>Tel&eacute;fono:</b> {{$orden->direccion->telefono}}<br>
                                        <b>Email:</b> {{$orden->direccion->email}}<br>
                                        <b>Forma De Entrega:</b> {{$orden->direccion->forma_entrega}}<br>
                                        @if( $orden->direccion->forma_entrega == null )
                      <b>Ciudad:</b> {{$orden->direccion->ciudad->nombre}}<br>
                                        <b>Calle Principal:</b> {{$orden->direccion->calle_principal}}<br>
                      <b>N&uacute;mero De Casa, Departamento, Oficina, Etc.:</b> {{$orden->direccion->numero_casa}}<br>
                      <b>Calle Secundaria:</b> {{$orden->direccion->calle_secundaria}}<br>
                      <b>Referencia E Indicaciones:</b> {{$orden->direccion->referencia}}<br>
                      @endif
                      <b>Comentarios:</b> {{$orden->direccion->comentario}}<br>
                      ¡Muchas gracias!<br>Equipo La Retratería

                                     </p>
                            <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                               <tr>
                                  <td style="padding: 0px 40px 40px 40px;">
                                     <hr style="border:none;border-top:1px solid #eaeaea;margin:26px 0;width:100%">
                                     <p style="font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#939598;font-weight:normal;text-align:justify;">La informaci&oacute;n contenida en este e-mail es confidencial para su destinatario. Esta informaci&oacute;n no debe ser distribuida ni copiada total o parcialmente por ning&uacute;n medio sin la autorizaci&oacute;n de laretrateriaec.com
                                     </p>
                                  </td>
                               </tr>
                            </table>
                         </td>
                      </tr>
                   </tbody>
                </table>
             </td>
          </tr>
       </tbody>
    </table>
    <div style="text-align:center;font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; color:#939598;font-weight:normal;">
      <a href="https://es-la.facebook.com/laretrateriaec/" target="_blank" style="padding:10px;"><i class="fa fa-facebook" style="font-size: 20px;"></i></a><a href="https://www.instagram.com/laretrateriaec/" target="_blank" style="padding:10px;"><i class="fa fa-instagram" style="font-size: 20px;"></i></a><a href="https://api.whatsapp.com/send?phone=593994625268&text=Quiero%20imprimir%20mis%20fotos" target="_blank" style="padding:10px;"><i class="fa fa-whatsapp" style="font-size: 20px;"></i></a>
      <br>
      Cuenca: Fernando de Arag&oacute;n 373. Telf: (099) 462 52 68
      <br>
      Email: <a href="mailto:info@laretrateriaec.com" style="color:#939598;text-decoration:none;">info@laretrateriaec.com</a>
      </div>