  <DIV id="id_1">
    <DIV id="id_1_1">
      <P class="p0 ft0">(INSTRUCTIONS ON REVERSE)</P>
    </DIV>
    <DIV id="id_1_2">
      <P class="p1 ft1">DEPARTMENT OF HOMELAND SECURITY</P>
      <P class="p2 ft2">U.S. Customs and Border Protection</P>
      <P class="p3 ft3">INWARD CARGO MANIFEST FOR VESSEL UNDER FIVE TONS, FERRY, TRAIN, CAR, VEHICLE, ETC.</P>
      <P class="p4 ft4">19 CFR 123.4, 123.7, 123.61</P>
    </DIV>
    <DIV id="id_1_3">
      <P class="p5 ft5">OMB Control Number: <NOBR>1651-0001</NOBR></P>
      <P class="p6 ft5">Expiration Date: 06/30/2015</P>
      <P class="p7 ft5">CBP Manifest/In Bond Number</P>
      <P class="p8 ft5">Page No.</P>
    </DIV>
  </DIV>
  <DIV id="id_2">
    <TABLE cellpadding=0 cellspacing=0 class="t0">
      <TR>
        <TD class="border">
          1. Name or Number and Description of Importing Conveyance
          <P class="user_inp">Mersedes-Bens sprinter FKM947  PQ Canada</P>
        </TD>
        <TD class="border" colspan="2">
          2. Name of Master or Person in Charge
        </TD>
      </TR>
      <TR>
        <TD class="border">
          3. Name and Address of Owner
          <P class="user_inp">
            <?php
              if($address->address_type==0){
            ?>
              <?=$address->first_name;?> <?=$address->last_name;?><br>
            <?php }else{?>
              <?=$address->company_name;?><br>
            <?php }?>

            <?=$address->city;?>
            <?=$address->state;?>
            <?=$address->adress_1;?> <?=$address->adress_2;?>
            <?=$address->zip;?>
          </P>
        </TD>
        <TD class="border border_h">
          4. Foreign Port of Lading
          <P class="user_inp">MTL</P>
        </TD>
        <TD class="border border_h">
          5. U.S. Port of Destination
          <P class="user_inp">CHM</P>
        </TD>
      </TR>
      <TR>
        <TD class="border">
          6. Port of Arrival
          <P class="user_inp">07 12/CHM</P>
        </TD>
        <TD class="border" colspan="2">
          7. Date of Arrival
          <P class="user_inp"><?=date('d-m-Y',$order->transport_data);?></P>
        </TD>
      </TR>
    </TABLE>
    <table>
      <TR>
        <TD class="tr4 td7"><P class="p11 ft7">&nbsp;</P></TD>
        <TD class="tr4 td35"><P class="p14 ft8">Column No. 1</P></TD>
        <TD class="tr4 td36"><P class="p15 ft8">Column No. 2</P></TD>
        <TD class="tr4 td10"><P class="p11 ft7">&nbsp;</P></TD>
        <TD colspan=2 class="tr4 td37"><P class="p16 ft8">Column No. 3</P></TD>
        <TD class="tr4 td38"><P class="p11 ft7">&nbsp;</P></TD>
        <TD class="tr4 td14"><P class="p11 ft7">&nbsp;</P></TD>
        <TD colspan=2 class="tr4 td39"><P class="p17 ft8">Column No. 4</P></TD>
        <TD class="tr4 td17"><P class="p18 ft8">Column No. 5</P></TD>
        <TD class="tr5 td18"><P class="p11 ft9">&nbsp;</P></TD>
      </TR>
      <TR>
        <TD colspan=2 class="tr6 td40"><P class="p15 ft5">Bill of Lading or Marks & </TD>
        <TD rowspan=2 class="tr7 td41"><P class="p19 ft5">Car Number</P></TD>
        <TD colspan=4 rowspan=2 class="tr7 td42"><P class="p20 ft10">Number and Gross Weight (in kilos or pounds) of</P></TD>
        <TD class="tr6 td43"><P class="p11 ft6">&nbsp;</P></TD>
        <TD colspan=2 rowspan=3 class="tr8 td44"><P class="p11 ft5">Name of Consignee</P></TD>
        <TD colspan=2 rowspan=3 class="tr8 td45"><P class="p21 ft5">For Use By CBP only</P></TD>
      </TR>
      <TR>
        <TD class="tr9 td19"><P class="p11 ft11">&nbsp;</P></TD>
        <TD rowspan=2 class="tr4 td46"><P class="p22 ft12">Numbers or Address of</P></TD>
        <TD class="tr9 td43"><P class="p11 ft11">&nbsp;</P></TD>
      </TR>
      <TR>
        <TD class="tr10 td19"><P class="p11 ft13">&nbsp;</P></TD>
        <TD rowspan=2 class="tr3 td41"><P class="p20 ft5">and Initials</P></TD>
        <TD colspan=4 rowspan=2 class="tr3 td42"><P class="p23 ft10">Packages and Description of Goods</P></TD>
        <TD class="tr10 td43"><P class="p11 ft13">&nbsp;</P></TD>
      </TR>
      <TR>
        <TD colspan=2 rowspan=2 class="tr3 td40"><P class="p15 ft5">Consignee on Packages</P></TD>
        <TD class="tr11 td43"><P class="p11 ft14">&nbsp;</P></TD>
        <TD class="tr11 td31"><P class="p11 ft14">&nbsp;</P></TD>
        <TD class="tr11 td47"><P class="p11 ft14">&nbsp;</P></TD>
        <TD class="tr11 td33"><P class="p11 ft14">&nbsp;</P></TD>
        <TD class="tr11 td18"><P class="p11 ft14">&nbsp;</P></TD>
      </TR>
      <TR>
        <TD class="tr10 td41"><P class="p11 ft13">&nbsp;</P></TD>
        <TD class="tr10 td21"><P class="p11 ft13">&nbsp;</P></TD>
        <TD class="tr10 td48"><P class="p11 ft13">&nbsp;</P></TD>
        <TD class="tr10 td23"><P class="p11 ft13">&nbsp;</P></TD>
        <TD class="tr10 td49"><P class="p11 ft13">&nbsp;</P></TD>
        <TD class="tr10 td43"><P class="p11 ft13">&nbsp;</P></TD>
        <TD class="tr10 td31"><P class="p11 ft13">&nbsp;</P></TD>
        <TD class="tr10 td47"><P class="p11 ft13">&nbsp;</P></TD>
        <TD class="tr10 td33"><P class="p11 ft13">&nbsp;</P></TD>
        <TD class="tr10 td18"><P class="p11 ft13">&nbsp;</P></TD>
      </TR>
      <TR>
        <TD class="tr12 td7"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td35"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td36"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td10"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td50"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td12"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td38"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td14"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td15"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td51"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr12 td17"><P class="p11 ft15">&nbsp;</P></TD>
        <TD class="tr9 td18"><P class="p11 ft11">&nbsp;</P></TD>
      </TR>
      <TR class="user_inp">
        <TD class="tr13 td35" colspan="2">
          <?=count($order_elements);?> individually packaged package
        </TD>
        <TD class="tr13 td36 td12">

        </TD>
        <TD class="tr13 td38" colspan="4">
          <?=count($order_elements);?> package, total amount <?=$total['price'];?>
        </TD>
        <TD class="tr13 td51" colspan="3">
          <?
            $include=$order_elements[0];
            if($include->address_type==1){
              echo $include->company_name.', ';
            }
            echo $include->first_name.' '.$include->last_name;
          ;?>
        </TD>
        <TD class="tr13 td17" colspan="2"><P class="p11 ft6">&nbsp;</P></TD>
      </TR>
    </TABLE>
    <TABLE cellpadding=0 cellspacing=0 class="t1">
      <TR>
        <TD class="tr15 td52"><P class="p11 ft6">&nbsp;</P></TD>
        <TD colspan=2 class="tr15 td53"><P class="p24 ft1">CARRIER'S CERTIFICATE</P></TD>
      </TR>
      <TR>
        <TD class="tr16 td52"><P class="p11 ft1">To the Port Director of CBP, Port of Arrival:</P></TD>
        <TD class="tr16 td54"><P class="p11 ft6">&nbsp;</P></TD>
        <TD class="tr16 td55"><P class="p11 ft6">&nbsp;</P></TD>
      </TR>
      <TR>
        <TD class="tr2 td52"><P class="p11 ft1">The undersigned carrier hereby certifies that</P></TD>
        <TD class="tr1 td56"><P class="p11 ft6">&nbsp;</P></TD>
        <TD class="tr2 td55"><P class="p25 ft1">of</P></TD>
      </TR>
    </TABLE>
    <P class="p26 ft1">is the owner or consignee of such articles within the purview of section 484, Tariff Act of 1930.</P>
    <P class="p27 ft1">I certify that this manifest is correct and true to the best of my knowledge.</P>
    <P class="p28 ft1">Date<SPAN style="padding-left:174px;">Master or Person in charge</SPAN></P>
    <P class="p29 ft0">(Signature)</P>
  </DIV>
  <DIV id="id_3">
    <TABLE cellpadding=0 cellspacing=0 class="t2">
      <TR>
        <TD class="tr17 td57"><P class="p11 ft16">Previous Editions are Obsolete</P></TD>
        <TD class="tr17 td33"><P class="p11 ft5">CBP Form 7533 (06/09)</P></TD>
      </TR>
    </TABLE>
  </DIV>
