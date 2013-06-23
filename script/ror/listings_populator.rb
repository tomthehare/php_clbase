# listings populator script

require_relative 'data_access'
require_relative 'craigslist_tool'
require_relative 'logger'
require_relative 'debug_print'

database_server = "127.0.0.1"
database_user = "root"
database_password = "braindrain"
database_name = "clbase_development"

clt = CraigsListTool.new()
log = Logger.new()

begin #error handling
	data_access = DataAccess.new(
		database_server, 
		database_user, 
		database_password, 
		database_name)

	log.log_exec("-------------------------------------")
	log.log_exec("started update at #{Time.now.to_s}")

	boston_locations = data_access.get_search_locations

	DebugPrint::print "found #{boston_locations.count} locations"

	boston_locations.each do |search_location|
		#scrub out the spaces in the string and replace with +
		search_location.gsub!(/\s/, '+')

		DebugPrint::print "processing #{search_location}..."

		#first do non-shared stuff
		url_string = "http://boston.craigslist.org/search/aap?zoomToPosting=&altView=&query=#{search_location}&srchType=A&minAsk=&maxAsk=1200&bedrooms=1&sort=date"

		# NOKOGIRI querying
		raw_listings = clt.query_for(search_location, url_string)
		puts "Retrieved #{raw_listings.length} results from #{search_location}"

		# PROCESS the listings
		processed_listings = clt.process_listings(raw_listings, search_location)
		puts "Processed #{processed_listings.length} listings from #{search_location}"

		# SAVE to the database
		data_access.insert_listings(processed_listings, false)

		#now do shared listing stuff #############################################
		url_string_share = "http://boston.craigslist.org/search/roo?zoomToPosting=&query=#{search_location}&srchType=A&minAsk=&maxAsk=1200"

		# NOKOGIRI querying
		raw_listings_share = clt.query_for(search_location, url_string_share)
		puts "Retrieved #{raw_listings_share.length} results from #{search_location} {SHARED}"

		# PROCESS the listings
		processed_listings_share = clt.process_listings(raw_listings_share, search_location)
		puts "Processed #{processed_listings_share.length} listings from #{search_location} {SHARED}"

		# SAVE to the database
		data_access.insert_listings(processed_listings_share, true)

	end #foreach location
rescue Mysql::Error => e
	error_message = "database write error>>#{e.to_s}"
 #    File.open("#{LOG_LOCATION}errors.log", "a") do |myfile|
	# 	myfile.puts(error_message)
	# 	myfile.puts(e.backtrace)
	# end
	log.log_error(error_message)
	log.log_error(e.backtrace)
	puts "ERROR: #{error_message}"
	puts e.backtrace
rescue Exception => e2
	error_message = "exception throw>>#{e2.to_s}"
	log.log_error(error_message)
	log.log_error(e2.backtrace)
	# File.open("#{LOG_LOCATION}errors.log", "a") do |myfile|
	# 	myfile.puts(error_message)
	# 	myfile.puts(e2.backtrace)
	# end
	puts "ERROR: #{error_message}"
	puts e2.backtrace
end

# Execution log entry
# File.open("#{LOG_LOCATION}execution.log", "a") do |myfile|
# 	myfile.puts("finished update at #{Time.now.to_s}")
# end
log.log_exec("finished update at #{Time.now.to_s}")
