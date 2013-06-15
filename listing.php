<?php

class Listing
{
	private $id;
	private $date;
	private $price;
	private $bedroom_count;
	private $location;
	private $image_flag;
	private $title;
	private $link;

	//need to figure out way to populate this
	private $favorite_flag;

	function Listing()
	{
		$this->favorite_flag = false;
	}

	//GETTERS/SETTERS
	function GetID()
	{
		return $this->id;
	}

	function SetID($id)
	{
		$this->id = $id;
	}

	function GetDate()
	{
		return $this->date;
	}

	function GetDateIntervalFromToday()
	{
		$date_now = new DateTime();
		$interval = $date_now->diff($this->date);
		return $interval;
	}

	function SetDate($date)
	{
		$this->date = new DateTime($date);
	}

	function GetPrice()
	{
		return $this->price;
	}

	function SetPrice($price)
	{
		$this->price = ($price == -1 ? "???" : $price);
	}

	function GetBedroomCount()
	{
		return $this->bedroom_count;
	}

	function SetBedroomCount($bedroom_count)
	{
		$this->bedroom_count = $bedroom_count;
	}

	function GetLocation()
	{
		return $this->location;
	}

	function SetLocation($location)
	{
		$this->location = $location;
	}

	function GetImageFlag()
	{
		return $this->image_flag;
	}

	function SetImageFlag($image_flag)
	{
		$this->image_flag = ($image_flag == 1 ? "Yes" : "No");
	}

	function GetFavoriteFlag()
	{
		return $this->favorite_flag;
	}

	function SetFavoriteFlag($favorite_flag)
	{
		$this->favorite_flag = ($favorite_flag == 1 ? true : false);
	}

	function GetTitle()
	{
		return $this->title;
	}

	function SetTitle($title)
	{
		$this->title = $title;
	}

	function GetLink()
	{
		return $this->link;
	}

	function SetLink($link)
	{
		$this->link = $link;
	}
}