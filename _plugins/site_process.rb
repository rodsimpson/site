#!/usr/bin/env ruby19


module Jekyll
	class Site
		def process
			self.reset
			self.read
			self.generate

						
			# gindex = self.generators.index do |x|
			# 	x.class == Pagination
			# end
			#         
			# pindex = pages.index do |x|
			# 	x.real_dir == '/blog' && x.basename = 'index.html'
			# end               
			# self.generators[gindex].paginate(self, pages[pindex]);    
			
			self.render 
			# these must come after render
			self.generate_tags_categories
			self.generate_archives
			self.cleanup
			self.write
			
			
		end
	end
end
 