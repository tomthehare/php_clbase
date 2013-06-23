# data access for listings database

require 'mysql'
require 'rubygems'
require 'date'
require_relative 'logger'
require_relative 'debug_print'

class DataAccess

 	@con = nil
    @server = nil
    @username = nil
    @password = nil
    @database_name = nil

    @log = nil

	def initialize(server, username, password, database_name)
		@server = server
		@username = username
		@password = password
		@database_name = database_name

		@log = Logger.new()
	end

	def insert_listings(listings_list, shared)
		begin
			@con = Mysql.new @server, @username, @password, @database_name

			if listings_list != nil
				for i in 0..listings_list.length-1
				    _title = listings_list[i]["title"]
				    _listing_date = DateTime.parse(listings_list[i]["listing_date"]).strftime('%Y-%m-%d %H:%M:%S')
				    _price = listings_list[i]["price"].to_i
				    _bedroom_count = listings_list[i]["bedrooms"].to_i
				    _location = listings_list[i]["location"]
				    _image = listings_list[i]["image"]
				    _viewed = false
				    _favorite = false
				    _deleted = false
				    _url = listings_list[i]["url"]
				    _created_at = DateTime.now.strftime('%Y-%m-%d %H:%M:%S')
				    _updated_at = DateTime.now.strftime('%Y-%m-%d %H:%M:%S')
				    _shared = shared
				    _query_location = listings_list[i]["query_location"]
					
				   	# TODO: make sprocs

					insert_query = %{
						insert into clbase_development.listings(
						`title`, 
						`listing_date`, 
						`price`, 
						`bedroom_count`, 
						`location`, 
						`image`, 
						`viewed`, 
						`favorite`,
						`url`,
						`created_at`,
						`updated_at`,
						`deleted`,
						`shared`,
						`query_location`)
						values('#{_title}', '#{_listing_date}', #{_price}, #{_bedroom_count}, '#{_location}', #{_image}, #{_viewed}, #{_favorite}, '#{_url}', '#{_created_at}', '#{_updated_at}', #{_deleted}, #{_shared}, '#{_query_location}')
				    }

				  #   update_shared_query = %{
				  #   	update clbase_development.listings set shared = 1
				  #   	WHERE title = '#{_title}'
						# AND listing_date = '#{_listing_date}'
				  #   }

				    retrieve_query = %{
				    	SELECT * from clbase_development.listings 
						WHERE title = '#{_title}'
						AND listing_date = '#{_listing_date}'
				    }


				    rs = @con.query(retrieve_query)

				    if(rs.num_rows == 0)
				    	@con.query(insert_query)
				    # else
				    # 	if(shared == true)
				    # 		@con.query(update_shared_query)
				    # 	end
					end
				end
			end
		rescue Exception => e2
			@log.log_error("Offending insert query:\n\n#{insert_query}\n\nOffending retrieve query:\n\n#{retrieve_query}")
		    raise e2
		ensure
			@con.close if @con #close connection if not nil?
			@con = nil if @con #set to nil if not nil
			# puts "closing db connection"
		end
	end

	def get_search_locations
		return_array = Array.new

		begin
			@con = Mysql.new @server, @username, @password, @database_name

			DebugPrint::print @con.inspect.to_s

		    retrieve_query = %{
		    	SELECT * from clbase_development.search_locations 
		    }

		    rs = @con.query(retrieve_query)

		    if(rs.num_rows > 0)
		    	DebugPrint::print 'more than 0 rows found in db'
		    	rs.each do |row|
		    		return_array.push(row[1])
		    	end
			end
		rescue Exception => e2
			@log.log_error("Offending retrieve query:\n\n#{retrieve_query}\n\n")
		    raise e2
		ensure
			@con.close if @con #close connection if not nil?
			@con = nil if @con #set to nil if not nil
			# puts "closing db connection"
		end

		return return_array
	end

	def print
		puts "Connection: #{@con}"
		puts "Server: #{@server}"
		puts "Username: #{@username}"
		puts "Password: #{@password}"
		puts "Database name: #{@database_name}"
	end
end