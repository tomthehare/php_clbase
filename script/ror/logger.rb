
require 'fileutils'

class Logger

	LOG_LOCATION = "C:/RubyOnRails/Repository/clbase/clbase/log/"
	EXE_LOG_PATH = "#{LOG_LOCATION}execution.log"
	ERR_LOG_PATH = "#{LOG_LOCATION}errors.log"

	def log_error(error)
		log(ERR_LOG_PATH, error)
	end

	def log_exec(message)
		log(EXE_LOG_PATH, message)
	end

	def log(path, message)
		File.open(path, "a") do |myfile|
			myfile.puts(message)
		end
	end

end