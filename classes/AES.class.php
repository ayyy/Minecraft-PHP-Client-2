<?php

/*

Incomplete AES class with buffering

*/


class AES{
	private $key, $encIV, $decIV, $IVLenght, $bytes;

	function __construct($bits, $mode, $blockSize){
		$this->mode = "AES-".$bits."-".$mode;
		$this->bytes = $blockSize / 8;
		$this->key = $this->encIV = $this->decIV = str_repeat("\x00", openssl_cipher_iv_length($this->mode));
		$this->IVLenght = openssl_cipher_iv_length($this->mode);
	}
	
	public function setKey($key){
		$this->key = str_pad($key, $this->IVLenght, "\x00", STR_PAD_RIGHT);
	}

	public function setIV($IV){
		$this->encIV = $this->decIV = str_pad($IV, $this->IVLenght, "\x00", STR_PAD_RIGHT);
	}
	
	protected function _shiftIV($IV, $str){ //Only for CFB
		if(!isset($str{$this->IVLenght - 1})){
			$len = min($this->IVLenght, strlen($str));
			return substr($IV, $len).substr($str, -$len);
		}
		return substr($str, -$this->IVLenght);
	}
	
	public function encrypt($plaintext){
		$ciphertext = openssl_encrypt($plaintext, $this->mode, $this->key, true, $this->encIV);
		$this->encIV = $this->_shiftIV($this->encIV, $ciphertext);
		return $ciphertext;
	}

	public function decrypt($ciphertext){
		$plaintext = openssl_decrypt($ciphertext, $this->mode, $this->key, true, $this->decIV);
		$this->decIV = $this->_shiftIV($this->decIV, $ciphertext);
		return $plaintext;
	}	

}