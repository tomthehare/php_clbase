class DebugPrint

	@debug_printer_active = true

	def self.print string
		if(@debug_printer_active)
			puts string
			puts ''
		end

	end
end