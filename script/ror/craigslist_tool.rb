# class to query cl using nokogiri

require 'net/http'
require 'mysql'
require 'rubygems'
require 'nokogiri'
require 'open-uri'
require 'date'
require 'fileutils'

class CraigsListTool

	@debug = false

	def query_for(search_location, url)

		listings = nil

		if(search_location != nil)
			#BEGIN NOKOGIRI
			page = Nokogiri::HTML(open(url))
			listings = page.css('p.row') # parse listings
		end

		return listings
	end

	def process_listings(raw_listings, search_location)

		date_class_name = "span.date"
		price_class_name = "span.price"
		a_href_offset_for_title = 1
		price_and_rooms_class_name = "span.pnr"
		image_class_name = 'span.p'

		# used to append to the relative paths retrieved from cl
		url_prefix = "http://boston.craigslist.org"

		processed_listings = nil

		if raw_listings != nil
			processed_listings = Array.new(raw_listings.length)

			#for i in 0...1
			for i in 0..raw_listings.length-1
				#what are we processing here?
				debug_print("RAW: " + raw_listings[i].text.inspect)

				before_processing = raw_listings[i].css(price_and_rooms_class_name)
				debug_print("Before processing var = " + before_processing.to_s)

				#need to scrub title for apostrophes
				title=raw_listings[i].css('a')[a_href_offset_for_title].text
				title.gsub!(/[\']/, '')
				debug_print("TITLE: " + title.inspect)

				price = nil
				if(raw_listings[i].css(price_class_name).text.strip[/\$\d+/] != nil)
					price = raw_listings[i].css(price_class_name).text.strip[/\$\d+/][/\d+/]
				end
				debug_print("PRICE: " + price.inspect)

				bedrooms= nil
				if(before_processing.text[/\d+br/] != nil)
					bedrooms = before_processing.text[/\d+br/][/\d+/]
				end
				debug_print("BEDROOMS: " + bedrooms.inspect)

				location = nil
				if(before_processing.text[/\(.+\)/] != nil)
					location = before_processing.text[/\(.+\)/].gsub(/\(/, '').gsub(/\)/, '').gsub(/[\+]/, ' ').gsub(/[\']/, '')
				end
				debug_print("SEARCH LOCATION: " + search_location.inspect)

				url=url_prefix + raw_listings[i].css('a')[a_href_offset_for_title]["href"]
				listing_date=raw_listings[i].css(date_class_name).text.strip + " #{Time.now.year}"
				
				debug_print("IMGTAGRAW: " + raw_listings[i].css(image_class_name).text.strip.inspect)
				image_found = raw_listings[i].css(image_class_name).text.strip[/img/]
				if(image_found == nil)
					image_found = raw_listings[i].css(image_class_name).text.strip[/pic/]
				end

				image = image_found != nil ? "true" : "false"
				debug_print("IMAGE FOUND: " + image)

				processed_listings[i] = { 
					"title"=>title,
					"price"=>(price == nil ? -1 : price),
					"bedrooms"=>(bedrooms == nil ? -1 : bedrooms),
					"url"=>url,
					"listing_date"=>listing_date,
					"image"=>image,
					"location"=>location,
					"query_location"=>search_location }

				debug_print("FINAL: " + processed_listings[i].inspect)
			end #for each listing
		end #if list null

		return processed_listings
	end

	def debug_print(string)
		# debug = false

		if(@debug)
			puts string
			puts ''
		end
	end

end