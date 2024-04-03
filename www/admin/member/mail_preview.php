<?

	include("mail/message.txt");

	function info($send, $recv)
	{
		global $subj;

		$info = "

            <TABLE cellpadding=1 cellspacing=0 border=0>

              <TR>
                <TD align=right><B><font size=2>발송&nbsp;</font></B></TD>
                <TD><font size=2>".$send[0]." &lt;".$send[1]."&gt;</font></TD>
              </TR>

              <TR>
                <TD align=right valign=top><B><font size=2>수신&nbsp;</font></B></TD>
                <TD><font size=2>".$recv[0]."님 &lt;".$recv[1]."&gt;</font></TD>
              </TR>

              <TR>
                <TD align=right valign=top><B><font size=2>제목&nbsp;</font></B></TD>
                <TD><font size=2>".$subj."</font></TD>
              </TR>

            </TABLE>

            <BR>

          <!-- 내용삽입 -->

		";

		return $info;
	}

	echo(info(array($sname, $smail), array('메일링유저','anywhere@domain.com')).$text);
?>
