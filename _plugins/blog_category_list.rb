module Jekyll

	class Site

		attr_accessor :cats, :dishes

		def reset
			self.cats = Hash.new { |hash, key| hash[key] = [] }
			self.dishes = Hash.new { |hash, key| hash[key] = [] }
			self.categories = Hash.new { |hash, key| hash[key] = [] }
		end

		alias_method :site_payload_recipe, :site_payload
		def site_payload
			p = site_payload_recipe
			p['site']['cats'] = post_attr_hash('cats')
			p['site']['dishes'] = post_attr_hash('dishes')
			p
		end

		alias_method :read_posts_recipe, :read_posts
		def read_posts(dir)

			read_posts_recipe dir
			self.posts.each do |p|

				p.categories |= p.cats | p.dishes

				p.cats.each { |pt| self.categories[pt] << p }
				p.dishes.each { |pt| self.categories[pt] << p }

			end

		end

	end

	class Post

		attr_accessor :cats, :dishes

		alias_method :initialize_recipe, :initialize
		def initialize(site, source, dir, name)
			initialize_recipe site, source, dir, name
			self.cats 	= self.data.pluralized_array("cat", "cats")
			self.dishes 		= self.data.pluralized_array("dish", "dishes")
		end

	end

  module Filters

    # Outputs a list of categories as comma-separated <a> links. This is used
    # to output the tag list for each post on a tag page.
    #
    #  +categories+ is the list of categories to format.
    #
    # Returns string
    def slugify(tag)
    	tag.strip.downcase.gsub(/(&|&amp;)/, ' and ').gsub(/[\s\.\/\\]/, '-').gsub(/[^\w-]/, '').gsub(/[-_]{2,}/, '-').gsub(/^[-_]/, '').gsub(/[-_]$/, '')
    end

    def browse_list(categories)

      html = String.new
		categories.keys.sort.each do |key|
			html << '<li><a href="/categories/' + slugify(key) + '/" class="tag-link">'+key+'</a></li>'
      end

		"#{html}"
    end

  end

end