<?php



/**
 * @author Nicolaas [at] sunnysideup.co.nz
 */


class NewsletterSignupDecorator extends DataExtension
{

    private static $many_many = array("MailingLists" => "MailingList");
}
