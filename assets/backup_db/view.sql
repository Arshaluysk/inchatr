CREATE
 ALGORITHM = UNDEFINED
 VIEW `view_usage_log`
 (id,module_id,user_id,usage_month,usage_year,usage_count)
 AS select * from usage_log where `usage_month`=MONTH(curdate()) and `usage_year`= YEAR(curdate()) ;



CREATE ALGORITHM = UNDEFINED VIEW `view_sms_sent_history_user_wise_total` (user_id,name,gateway_name,email,mobile,total_sms_sent) 
AS SELECT sms_history.user_id, name, gateway_name, email, phone_number, count( sms_history.id ) AS total_sms_sent 
FROM sms_history 
LEFT JOIN users ON ( users.id = sms_history.user_id ) 
LEFT JOIN sms_api_config ON (sms_api_config.id = sms_history.gateway_id) 
GROUP BY sms_history.user_id;



CREATE ALGORITHM = UNDEFINED VIEW `view_sms_sent_history_user_wise_this_month` (user_id,month_name,name,gatway_name,email,mobile,total_sms_sent) 
AS select sms_history.user_id,MONTHNAME(sent_time) as month_name,name,gateway_name,email,phone_number,count(sms_history.id) as total_sms_sent from sms_history 
LEFT JOIN users on (users.id=sms_history.user_id) 
LEFT JOIN sms_api_config ON (sms_api_config.id = sms_history.gateway_id) 
where month(sent_time)=month(CURDATE()) group by user_id; 



CREATE ALGORITHM = UNDEFINED VIEW `view_month_user_sms_history` (user_id,month_name,month_number,years,name,gateway_name,email,mobile,total_sms_sent) 
AS select sms_history.user_id,MONTHNAME(sent_time) as month_name,MONTH(sent_time), YEAR(sent_time) as years, name,gateway_name,email,phone_number,count(sms_history.id) as total_sms_sent 
from sms_history 
LEFT JOIN users on (users.id=sms_history.user_id) 
LEFT JOIN sms_api_config ON (sms_api_config.id = sms_history.gateway_id) 
group by user_id, MONTH(sent_time), YEAR(sent_time); 


CREATE ALGORITHM = UNDEFINED VIEW `view_email_sent_history_user_wise_total` (user_id,name,email,total_email_sent) 
AS SELECT email_history.user_id, name, email, count( email_history.id ) AS total_email_sent 
FROM email_history 
LEFT JOIN users ON ( users.id = email_history.user_id ) 
GROUP BY email_history.user_id;



CREATE ALGORITHM = UNDEFINED VIEW `view_email_sent_history_user_wise_this_month` (user_id,month_name,name,email,total_email_sent) 
AS select email_history.user_id,MONTHNAME(sent_time) as month_name,name,email,count(email_history.id) as total_email_sent 
from email_history 
LEFT JOIN users on (users.id=email_history.user_id) 
where month(sent_time)=month(CURDATE()) group by user_id; 


CREATE ALGORITHM = UNDEFINED VIEW `view_month_user_email_history` (user_id,month_name,month_number,years,name,email,total_email_sent) 
AS select email_history.user_id,MONTHNAME(sent_time) as month_name,MONTH(sent_time), YEAR(sent_time) as years, name,email,count(email_history.id) as total_email_sent 
from email_history 
LEFT JOIN users on (users.id=email_history.user_id) 
group by user_id, MONTH(sent_time), YEAR(sent_time); 