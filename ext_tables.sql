#
# Table structure for table 'tx_bpnrequestaccess_domain_model_request'
#
CREATE TABLE tx_bpnrequestaccess_domain_model_request (
    uid int(11) NOT NULL auto_increment,
    pid int(11) DEFAULT 0 NOT NULL,
    tstamp int(11) DEFAULT 0 NOT NULL,
    crdate int(11) DEFAULT 0 NOT NULL,
    cruser_id int(11) DEFAULT 0 NOT NULL,
    deleted tinyint(4) DEFAULT 0 NOT NULL,
    hidden tinyint(4) DEFAULT 0 NOT NULL,

    title varchar(255) DEFAULT '' NOT NULL,
    start int(11) DEFAULT 0 NOT NULL,
    duration varchar(64) DEFAULT '' NOT NULL,
    user_request_target int(11) DEFAULT 0 NOT NULL,
    user_request_source int(11) DEFAULT 0 NOT NULL,
    usergroup int(11) DEFAULT 0 NOT NULL,
    request_result int(11) DEFAULT 0 NOT NULL,
    verification_code tinytext,

    PRIMARY KEY (uid),
    KEY parent (pid)
);
