<?php


class NewsletterSignupDecoratorFormFixes extends Extension
{

    public function updateFields(&$fields)
    {
        $newsletterObject = NewsletterSignup_Step::get()->first();
        if ($newsletterObject) {
            $mailingLists = $newsletterObject->MailingLists();
            $fields->push(new HeaderField("NewsletterSignupHeader", _t("OrderForm.NEWSLETTER", "Newsletter"), 3));
            $fields->push(new LiteralField("NewsletterSignupContent", "<p>"._t("OrderForm.SIGNUPTEASER", "Sign up to our newsletter to receive updates as they are released.")."</p>"));
            $fields->push(new CheckboxSetField("MailingLists", _t("OrderForm.SIGNUPTONEWSLETTER", "Sign up"), $mailingLists->map()));
        }
    }
}
