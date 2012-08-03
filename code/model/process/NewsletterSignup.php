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
		$fields->addFieldToTab("Root.Main", new HeaderField("InformAdminAboutNewsletter", _t("OrderStep.INFORMADMINABOUTNEWSLETTER", "Inform admin about newsletter"), 3), "SendMessageToAdmin");
		$fields->replaceField("SendCopyTo", new EmailField(new EmailField("SendCopyTo", _t("OrderStep.SENDCOPYTO", "Send a copy (another e-mail) to ..."));
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
					$email = new Email(
						$from = Order_Email::get_from_email(),
						$to = Order_Email::get_from_email(),
						$subject = "newsletter registration update",
						$body = "Email: ".$member->Email.", Sign-up: ".($member->NewsletterSignup ? "YES" : "NO")
								."<br /><br /><br />".print_r($order->BillingAddress, 1);
					);
					$email->send();
					//copy!
					if($this->SendCopyTo){
						$email = new Email(
							$from = Order_Email::get_from_email(),
							$to = $this->SendCopyTo,
							$subject = "newsletter registration update",
							$body = "Email: ".$member->Email.", Sign-up: ".($member->NewsletterSignup ? "YES" : "NO")
									."<br /><br /><br />".print_r($order->BillingAddress, 1);
						);
						$email->send();
					}
				}
			}
		}
		$member->extend("updateNewsletterStatus", $member);
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
