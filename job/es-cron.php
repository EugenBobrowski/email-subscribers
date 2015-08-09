<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if(isset($_GET['es']))
{
	if($_GET['es'] == "cron")
	{
		$es_c_cronurl = get_option('es_c_cronurl');	
		$es_c_croncount = get_option('es_cron_mailcount');	
		
		// Get URL guid for verification
		$es_c_cronguid = isset($_GET['guid']) ? $_GET['guid'] : '';
		$es_c_cronguid = trim($es_c_cronguid);
		
		parse_str($es_c_cronurl, $output);
		
		//echo $es_c_cronguid . "--<br>";
		//echo $output['guid'] . "--<br>";
		
		if($es_c_cronguid == $output['guid'])
		{
			if(!is_numeric($es_c_croncount))
			{
				$es_c_croncount = 50;
			}
			
			$cronmailqueue = es_cls_sentmail::es_sentmail_cronmail_inqueue();
			//echo $cronmailqueue[0]['es_sent_guid'] . "--";
			if(count($cronmailqueue) > 0)
			{
				$crondeliveryqueue = es_cls_delivery::es_delivery_cronmail_inqueue($es_c_croncount, $cronmailqueue[0]['es_sent_guid']);
				if(count($crondeliveryqueue) > 0)
				{
					es_cls_sendmail::es_prepare_send_cronmail($cronmailqueue, $crondeliveryqueue);
				}
				
				$cronmailqueuecnt = es_cls_delivery::es_delivery_cronmail_count($cronmailqueue[0]['es_sent_guid']);
				if($cronmailqueuecnt == 0)
				{
					es_cls_sentmail::es_sentmail_cronmail_ups($cronmailqueue[0]['es_sent_guid']);
				}
			}
		}
		else
		{
			// Invalid call
		}
	}
	else
	{
		// Invalid call
	}
}
else
{
	// Invalid call
}
die();
?>