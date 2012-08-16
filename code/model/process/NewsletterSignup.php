<?php



class NewsletterSignup_Step extends OrderStep {

	static $db = array(
		"SendMessageToAdmin" => "Boolean",
		"SendCopyTo" => "Varchar(255)"
	);

	public static $defaults = array(
		"CustomerCanEdit" => 0,
		"CustomerCanCancel" => 0,
		"CustomerCanPay" => 1,
		"Name" => "Update Newsletter Status",
		"Code" => "NEWSLETTERSTATUS",
		"Sort" => 26,
		"ShowAsInProcessOrder" => 1,
		"SendMessageToAdmin" => 1
	);

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->addFieldToTab("Root.Main", new HeaderField("InformAdminAboutNewsletter", _t("OrderStep.EMAILDETAILSTO", "Email details to"), 3), "SendMessageToAdmin");
		$fields->replaceField("SendCopyTo", new EmailField("SendCopyTo", _t("OrderStep.SENDCOPYTO", "Send a copy (another e-mail) to ...")));
		return $fields;
	}

	/**
	 * can run step once order has been submitted.
	 * NOTE: must have a payment (even if it is a fake payment).
	 * The reason for this is if people pay straight away then they want to see the payment shown on their invoice.
	 * @param DataObject $order Order
	 * @return Boolean
	 **/
	public function initStep($order) {
		return true;
	}

	/**
	 * emailing admin and or running custom code to update newsletter status
	 * @param DataObject $order Order
	 * @return Boolean
	 **/
	public function doStep($order) {
		if($this->SendMessageToAdmin){
			$member = $order->Member();
			if($member) {
				if($member->NewsletterSignup) {
					$from = Order_Email::get_from_email();
					$subject = _t("NewsletterSignup.NEWSLETTERREGISTRATIONUPDATE", "newsletter registration update");
					$billingAddressOutput = "";
					$billingAddress = $order->BillingAddress();
					if($billingAddress) {
						$billingAddressOutput = $billingAddress->renderWith("Order_AddressBilling");
					}
					$body = "
						"._t("NewsletterSignup.EMAIL", "Email").": <strong>".$member->Email."</strong>".
						"<br /><br />"._t("NewsletterSignup.SIGNUP", "Signed Up").": <strong>".($member->NewsletterSignup ? _t("NewsletterSignup.YES", "Yes") : _t("NewsletterSignup.NO", "No"))."</strong>".
						"<br /><br />".$billingAddressOutput;
					$email = new Email(
						$from,
						$to = Order_Email::get_from_email(),
						$subject,
						$body
					);
					$email->send();
					//copy!
					if($this->SendCopyTo){
						$email = new Email(
							$from,
							$to = $this->SendCopyTo,
							$subject,
							$body
						);
						$email->send();
					}
				}
				//this can be used to connect with third parties (e.g. )
				$this->extend("updateNewsletterStatus", $member);
			}
		}
		return true;
	}

	/**
	 * can do next step once the invoice has been sent or in case the invoice does not need to be sent.
	 * @param DataObject $order Order
	 * @return DataObject | Null	(next step OrderStep object)
	 **/
	public function nextStep($order) {
		return parent::nextStep($order);
	}

	/**
	 * Allows the opportunity for the Order Step to add any fields to Order::getCMSFields
	 *@param FieldSet $fields
	 *@param Order $order
	 *@return FieldSet
	 **/
	function addOrderStepFields(&$fields, $order) {
		$fields = parent::addOrderStepFields($fields, $order);
		return $fields;
	}

	/**
	 * For some ordersteps this returns true...
	 * @return Boolean
	 **/
	protected function hasCustomerMessage() {
		return false;
	}

	/**
	 * Explains the current order step.
	 * @return String
	 */
	protected function myDescription(){
		return _t("OrderStep.NEWSLETTERSTATUS_DESCRIPTION", "Update newsletter status.");
	}

}
