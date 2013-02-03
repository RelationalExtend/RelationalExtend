/* ===================================================
 * bootstrap-transition.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#transitions
 * ===================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


  /* CSS TRANSITION SUPPORT (http://www.modernizr.com/)
   * ======================================================= */

  $(function () {

    $.support.transition = (function () {

      var transitionEnd = (function () {

        var el = document.createElement('bootstrap')
          , transEndEventNames = {
               'WebkitTransition' : 'webkitTransitionEnd'
            ,  'MozTransition'    : 'transitionend'
            ,  'OTransition'      : 'oTransitionEnd otransitionend'
            ,  'transition'       : 'transitionend'
            }
          , name

        for (name in transEndEventNames){
          if (el.style[name] !== undefined) {
            return transEndEventNames[name]
          }
        }

      }())

      return transitionEnd && {
        end: transitionEnd
      }

    })()

  })

}(window.jQuery);
/* =========================================================
 * bootstrap-modal.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#modals
 * =========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */


!function ($) {

  "use strict"; // jshint ;_;


 /* MODAL CLASS DEFINITION
  * ====================== */

  var Modal = function (element, options) {
    this.options = options
    this.$element = $(element)
      .delegate('[data-dismiss="modal"]', 'click.dismiss.modal', $.proxy(this.hide, this))
    this.options.remote && this.$element.find('.modal-body').load(this.options.remote)
  }

  Modal.prototype = {

      constructor: Modal

    , toggle: function () {
        return this[!this.isShown ? 'show' : 'hide']()
      }

    , show: function () {
        var that = this
          , e = $.Event('show')

        this.$element.trigger(e)

        if (this.isShown || e.isDefaultPrevented()) return

        this.isShown = true

        this.escape()

        this.backdrop(function () {
          var transition = $.support.transition && that.$element.hasClass('fade')

          if (!that.$element.parent().length) {
            that.$element.appendTo(document.body) //don't move modals dom position
          }

          that.$element
            .show()

          if (transition) {
            that.$element[0].offsetWidth // force reflow
          }

          that.$element
            .addClass('in')
            .attr('aria-hidden', false)

          that.enforceFocus()

          transition ?
            that.$element.one($.support.transition.end, function () { that.$element.focus().trigger('shown') }) :
            that.$element.focus().trigger('shown')

        })
      }

    , hide: function (e) {
        e && e.preventDefault()

        var that = this

        e = $.Event('hide')

        this.$element.trigger(e)

        if (!this.isShown || e.isDefaultPrevented()) return

        this.isShown = false

        this.escape()

        $(document).off('focusin.modal')

        this.$element
          .removeClass('in')
          .attr('aria-hidden', true)

        $.support.transition && this.$element.hasClass('fade') ?
          this.hideWithTransition() :
          this.hideModal()
      }

    , enforceFocus: function () {
        var that = this
        $(document).on('focusin.modal', function (e) {
          if (that.$element[0] !== e.target && !that.$element.has(e.target).length) {
            that.$element.focus()
          }
        })
      }

    , escape: function () {
        var that = this
        if (this.isShown && this.options.keyboard) {
          this.$element.on('keyup.dismiss.modal', function ( e ) {
            e.which == 27 && that.hide()
          })
        } else if (!this.isShown) {
          this.$element.off('keyup.dismiss.modal')
        }
      }

    , hideWithTransition: function () {
        var that = this
          , timeout = setTimeout(function () {
              that.$element.off($.support.transition.end)
              that.hideModal()
            }, 500)

        this.$element.one($.support.transition.end, function () {
          clearTimeout(timeout)
          that.hideModal()
        })
      }

    , hideModal: function (that) {
        this.$element
          .hide()
          .trigger('hidden')

        this.backdrop()
      }

    , removeBackdrop: function () {
        this.$backdrop.remove()
        this.$backdrop = null
      }

    , backdrop: function (callback) {
        var that = this
          , animate = this.$element.hasClass('fade') ? 'fade' : ''

        if (this.isShown && this.options.backdrop) {
          var doAnimate = $.support.transition && animate

          this.$backdrop = $('<div class="modal-backdrop ' + animate + '" />')
            .appendTo(document.body)

          this.$backdrop.click(
            this.options.backdrop == 'static' ?
              $.proxy(this.$element[0].focus, this.$element[0])
            : $.proxy(this.hide, this)
          )

          if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

          this.$backdrop.addClass('in')

          doAnimate ?
            this.$backdrop.one($.support.transition.end, callback) :
            callback()

        } else if (!this.isShown && this.$backdrop) {
          this.$backdrop.removeClass('in')

          $.support.transition && this.$element.hasClass('fade')?
            this.$backdrop.one($.support.transition.end, $.proxy(this.removeBackdrop, this)) :
            this.removeBackdrop()

        } else if (callback) {
          callback()
        }
      }
  }


 /* MODAL PLUGIN DEFINITION
  * ======================= */

  var old = $.fn.modal

  $.fn.modal = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('modal')
        , options = $.extend({}, $.fn.modal.defaults, $this.data(), typeof option == 'object' && option)
      if (!data) $this.data('modal', (data = new Modal(this, options)))
      if (typeof option == 'string') data[option]()
      else if (options.show) data.show()
    })
  }

  $.fn.modal.defaults = {
      backdrop: true
    , keyboard: true
    , show: true
  }

  $.fn.modal.Constructor = Modal


 /* MODAL NO CONFLICT
  * ================= */

  $.fn.modal.noConflict = function () {
    $.fn.modal = old
    return this
  }


 /* MODAL DATA-API
  * ============== */

  $(document).on('click.modal.data-api', '[data-toggle="modal"]', function (e) {
    var $this = $(this)
      , href = $this.attr('href')
      , $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) //strip for ie7
      , option = $target.data('modal') ? 'toggle' : $.extend({ remote:!/#/.test(href) && href }, $target.data(), $this.data())

    e.preventDefault()

    $target
      .modal(option)
      .one('hide', function () {
        $this.focus()
      })
  })

}(window.jQuery);

/* ============================================================
 * bootstrap-dropdown.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#dropdowns
 * ============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function ($) {

  "use strict"; // jshint ;_;


 /* DROPDOWN CLASS DEFINITION
  * ========================= */

  var toggle = '[data-toggle=dropdown]'
    , Dropdown = function (element) {
        var $el = $(element).on('click.dropdown.data-api', this.toggle)
        $('html').on('click.dropdown.data-api', function () {
          $el.parent().removeClass('open')
        })
      }

  Dropdown.prototype = {

    constructor: Dropdown

  , toggle: function (e) {
      var $this = $(this)
        , $parent
        , isActive

      if ($this.is('.disabled, :disabled')) return

      $parent = getParent($this)

      isActive = $parent.hasClass('open')

      clearMenus()

      if (!isActive) {
        $parent.toggleClass('open')
      }

      $this.focus()

      return false
    }

  , keydown: function (e) {
      var $this
        , $items
        , $active
        , $parent
        , isActive
        , index

      if (!/(38|40|27)/.test(e.keyCode)) return

      $this = $(this)

      e.preventDefault()
      e.stopPropagation()

      if ($this.is('.disabled, :disabled')) return

      $parent = getParent($this)

      isActive = $parent.hasClass('open')

      if (!isActive || (isActive && e.keyCode == 27)) return $this.click()

      $items = $('[role=menu] li:not(.divider):visible a', $parent)

      if (!$items.length) return

      index = $items.index($items.filter(':focus'))

      if (e.keyCode == 38 && index > 0) index--                                        // up
      if (e.keyCode == 40 && index < $items.length - 1) index++                        // down
      if (!~index) index = 0

      $items
        .eq(index)
        .focus()
    }

  }

  function clearMenus() {
    $(toggle).each(function () {
      getParent($(this)).removeClass('open')
    })
  }

  function getParent($this) {
    var selector = $this.attr('data-target')
      , $parent

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    $parent = $(selector)
    $parent.length || ($parent = $this.parent())

    return $parent
  }


  /* DROPDOWN PLUGIN DEFINITION
   * ========================== */

  var old = $.fn.dropdown

  $.fn.dropdown = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('dropdown')
      if (!data) $this.data('dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  $.fn.dropdown.Constructor = Dropdown


 /* DROPDOWN NO CONFLICT
  * ==================== */

  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }


  /* APPLY TO STANDARD DROPDOWN ELEMENTS
   * =================================== */

  $(document)
    .on('click.dropdown.data-api touchstart.dropdown.data-api', clearMenus)
    .on('click.dropdown touchstart.dropdown.data-api', '.dropdown form', function (e) { e.stopPropagation() })
    .on('touchstart.dropdown.data-api', '.dropdown-menu', function (e) { e.stopPropagation() })
    .on('click.dropdown.data-api touchstart.dropdown.data-api'  , toggle, Dropdown.prototype.toggle)
    .on('keydown.dropdown.data-api touchstart.dropdown.data-api', toggle + ', [role=menu]' , Dropdown.prototype.keydown)

}(window.jQuery);
/* =============================================================
 * bootstrap-scrollspy.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#scrollspy
 * =============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* SCROLLSPY CLASS DEFINITION
  * ========================== */

  function ScrollSpy(element, options) {
    var process = $.proxy(this.process, this)
      , $element = $(element).is('body') ? $(window) : $(element)
      , href
    this.options = $.extend({}, $.fn.scrollspy.defaults, options)
    this.$scrollElement = $element.on('scroll.scroll-spy.data-api', process)
    this.selector = (this.options.target
      || ((href = $(element).attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) //strip for ie7
      || '') + ' .nav li > a'
    this.$body = $('body')
    this.refresh()
    this.process()
  }

  ScrollSpy.prototype = {

      constructor: ScrollSpy

    , refresh: function () {
        var self = this
          , $targets

        this.offsets = $([])
        this.targets = $([])

        $targets = this.$body
          .find(this.selector)
          .map(function () {
            var $el = $(this)
              , href = $el.data('target') || $el.attr('href')
              , $href = /^#\w/.test(href) && $(href)
            return ( $href
              && $href.length
              && [[ $href.position().top + self.$scrollElement.scrollTop(), href ]] ) || null
          })
          .sort(function (a, b) { return a[0] - b[0] })
          .each(function () {
            self.offsets.push(this[0])
            self.targets.push(this[1])
          })
      }

    , process: function () {
        var scrollTop = this.$scrollElement.scrollTop() + this.options.offset
          , scrollHeight = this.$scrollElement[0].scrollHeight || this.$body[0].scrollHeight
          , maxScroll = scrollHeight - this.$scrollElement.height()
          , offsets = this.offsets
          , targets = this.targets
          , activeTarget = this.activeTarget
          , i

        if (scrollTop >= maxScroll) {
          return activeTarget != (i = targets.last()[0])
            && this.activate ( i )
        }

        for (i = offsets.length; i--;) {
          activeTarget != targets[i]
            && scrollTop >= offsets[i]
            && (!offsets[i + 1] || scrollTop <= offsets[i + 1])
            && this.activate( targets[i] )
        }
      }

    , activate: function (target) {
        var active
          , selector

        this.activeTarget = target

        $(this.selector)
          .parent('.active')
          .removeClass('active')

        selector = this.selector
          + '[data-target="' + target + '"],'
          + this.selector + '[href="' + target + '"]'

        active = $(selector)
          .parent('li')
          .addClass('active')

        if (active.parent('.dropdown-menu').length)  {
          active = active.closest('li.dropdown').addClass('active')
        }

        active.trigger('activate')
      }

  }


 /* SCROLLSPY PLUGIN DEFINITION
  * =========================== */

  var old = $.fn.scrollspy

  $.fn.scrollspy = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('scrollspy')
        , options = typeof option == 'object' && option
      if (!data) $this.data('scrollspy', (data = new ScrollSpy(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.scrollspy.Constructor = ScrollSpy

  $.fn.scrollspy.defaults = {
    offset: 10
  }


 /* SCROLLSPY NO CONFLICT
  * ===================== */

  $.fn.scrollspy.noConflict = function () {
    $.fn.scrollspy = old
    return this
  }


 /* SCROLLSPY DATA-API
  * ================== */

  $(window).on('load', function () {
    $('[data-spy="scroll"]').each(function () {
      var $spy = $(this)
      $spy.scrollspy($spy.data())
    })
  })

}(window.jQuery);
/* ========================================================
 * bootstrap-tab.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#tabs
 * ========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ======================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* TAB CLASS DEFINITION
  * ==================== */

  var Tab = function (element) {
    this.element = $(element)
  }

  Tab.prototype = {

    constructor: Tab

  , show: function () {
      var $this = this.element
        , $ul = $this.closest('ul:not(.dropdown-menu)')
        , selector = $this.attr('data-target')
        , previous
        , $target
        , e

      if (!selector) {
        selector = $this.attr('href')
        selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
      }

      if ( $this.parent('li').hasClass('active') ) return

      previous = $ul.find('.active:last a')[0]

      e = $.Event('show', {
        relatedTarget: previous
      })

      $this.trigger(e)

      if (e.isDefaultPrevented()) return

      $target = $(selector)

      this.activate($this.parent('li'), $ul)
      this.activate($target, $target.parent(), function () {
        $this.trigger({
          type: 'shown'
        , relatedTarget: previous
        })
      })
    }

  , activate: function ( element, container, callback) {
      var $active = container.find('> .active')
        , transition = callback
            && $.support.transition
            && $active.hasClass('fade')

      function next() {
        $active
          .removeClass('active')
          .find('> .dropdown-menu > .active')
          .removeClass('active')

        element.addClass('active')

        if (transition) {
          element[0].offsetWidth // reflow for transition
          element.addClass('in')
        } else {
          element.removeClass('fade')
        }

        if ( element.parent('.dropdown-menu') ) {
          element.closest('li.dropdown').addClass('active')
        }

        callback && callback()
      }

      transition ?
        $active.one($.support.transition.end, next) :
        next()

      $active.removeClass('in')
    }
  }


 /* TAB PLUGIN DEFINITION
  * ===================== */

  var old = $.fn.tab

  $.fn.tab = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('tab')
      if (!data) $this.data('tab', (data = new Tab(this)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.tab.Constructor = Tab


 /* TAB NO CONFLICT
  * =============== */

  $.fn.tab.noConflict = function () {
    $.fn.tab = old
    return this
  }


 /* TAB DATA-API
  * ============ */

  $(document).on('click.tab.data-api', '[data-toggle="tab"], [data-toggle="pill"]', function (e) {
    e.preventDefault()
    $(this).tab('show')
  })

}(window.jQuery);
/* ===========================================================
 * bootstrap-tooltip.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#tooltips
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ===========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* TOOLTIP PUBLIC CLASS DEFINITION
  * =============================== */

  var Tooltip = function (element, options) {
    this.init('tooltip', element, options)
  }

  Tooltip.prototype = {

    constructor: Tooltip

  , init: function (type, element, options) {
      var eventIn
        , eventOut

      this.type = type
      this.$element = $(element)
      this.options = this.getOptions(options)
      this.enabled = true

      if (this.options.trigger == 'click') {
        this.$element.on('click.' + this.type, this.options.selector, $.proxy(this.toggle, this))
      } else if (this.options.trigger != 'manual') {
        eventIn = this.options.trigger == 'hover' ? 'mouseenter' : 'focus'
        eventOut = this.options.trigger == 'hover' ? 'mouseleave' : 'blur'
        this.$element.on(eventIn + '.' + this.type, this.options.selector, $.proxy(this.enter, this))
        this.$element.on(eventOut + '.' + this.type, this.options.selector, $.proxy(this.leave, this))
      }

      this.options.selector ?
        (this._options = $.extend({}, this.options, { trigger: 'manual', selector: '' })) :
        this.fixTitle()
    }

  , getOptions: function (options) {
      options = $.extend({}, $.fn[this.type].defaults, options, this.$element.data())

      if (options.delay && typeof options.delay == 'number') {
        options.delay = {
          show: options.delay
        , hide: options.delay
        }
      }

      return options
    }

  , enter: function (e) {
      var self = $(e.currentTarget)[this.type](this._options).data(this.type)

      if (!self.options.delay || !self.options.delay.show) return self.show()

      clearTimeout(this.timeout)
      self.hoverState = 'in'
      this.timeout = setTimeout(function() {
        if (self.hoverState == 'in') self.show()
      }, self.options.delay.show)
    }

  , leave: function (e) {
      var self = $(e.currentTarget)[this.type](this._options).data(this.type)

      if (this.timeout) clearTimeout(this.timeout)
      if (!self.options.delay || !self.options.delay.hide) return self.hide()

      self.hoverState = 'out'
      this.timeout = setTimeout(function() {
        if (self.hoverState == 'out') self.hide()
      }, self.options.delay.hide)
    }

  , show: function () {
      var $tip
        , inside
        , pos
        , actualWidth
        , actualHeight
        , placement
        , tp

      if (this.hasContent() && this.enabled) {
        $tip = this.tip()
        this.setContent()

        if (this.options.animation) {
          $tip.addClass('fade')
        }

        placement = typeof this.options.placement == 'function' ?
          this.options.placement.call(this, $tip[0], this.$element[0]) :
          this.options.placement

        inside = /in/.test(placement)

        $tip
          .detach()
          .css({ top: 0, left: 0, display: 'block' })
          .insertAfter(this.$element)

        pos = this.getPosition(inside)

        actualWidth = $tip[0].offsetWidth
        actualHeight = $tip[0].offsetHeight

        switch (inside ? placement.split(' ')[1] : placement) {
          case 'bottom':
            tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'top':
            tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'left':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}
            break
          case 'right':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}
            break
        }

        $tip
          .offset(tp)
          .addClass(placement)
          .addClass('in')
      }
    }

  , setContent: function () {
      var $tip = this.tip()
        , title = this.getTitle()

      $tip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
      $tip.removeClass('fade in top bottom left right')
    }

  , hide: function () {
      var that = this
        , $tip = this.tip()

      $tip.removeClass('in')

      function removeWithAnimation() {
        var timeout = setTimeout(function () {
          $tip.off($.support.transition.end).detach()
        }, 500)

        $tip.one($.support.transition.end, function () {
          clearTimeout(timeout)
          $tip.detach()
        })
      }

      $.support.transition && this.$tip.hasClass('fade') ?
        removeWithAnimation() :
        $tip.detach()

      return this
    }

  , fixTitle: function () {
      var $e = this.$element
      if ($e.attr('title') || typeof($e.attr('data-original-title')) != 'string') {
        $e.attr('data-original-title', $e.attr('title') || '').removeAttr('title')
      }
    }

  , hasContent: function () {
      return this.getTitle()
    }

  , getPosition: function (inside) {
      return $.extend({}, (inside ? {top: 0, left: 0} : this.$element.offset()), {
        width: this.$element[0].offsetWidth
      , height: this.$element[0].offsetHeight
      })
    }

  , getTitle: function () {
      var title
        , $e = this.$element
        , o = this.options

      title = $e.attr('data-original-title')
        || (typeof o.title == 'function' ? o.title.call($e[0]) :  o.title)

      return title
    }

  , tip: function () {
      return this.$tip = this.$tip || $(this.options.template)
    }

  , validate: function () {
      if (!this.$element[0].parentNode) {
        this.hide()
        this.$element = null
        this.options = null
      }
    }

  , enable: function () {
      this.enabled = true
    }

  , disable: function () {
      this.enabled = false
    }

  , toggleEnabled: function () {
      this.enabled = !this.enabled
    }

  , toggle: function (e) {
      var self = $(e.currentTarget)[this.type](this._options).data(this.type)
      self[self.tip().hasClass('in') ? 'hide' : 'show']()
    }

  , destroy: function () {
      this.hide().$element.off('.' + this.type).removeData(this.type)
    }

  }


 /* TOOLTIP PLUGIN DEFINITION
  * ========================= */

  var old = $.fn.tooltip

  $.fn.tooltip = function ( option ) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('tooltip')
        , options = typeof option == 'object' && option
      if (!data) $this.data('tooltip', (data = new Tooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.tooltip.Constructor = Tooltip

  $.fn.tooltip.defaults = {
    animation: true
  , placement: 'top'
  , selector: false
  , template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
  , trigger: 'hover'
  , title: ''
  , delay: 0
  , html: false
  }


 /* TOOLTIP NO CONFLICT
  * =================== */

  $.fn.tooltip.noConflict = function () {
    $.fn.tooltip = old
    return this
  }

}(window.jQuery);
<!DOCTYPE html>
<!--

Hello future GitHubber! I bet you're here to remove those nasty inline styles,
DRY up these templates and make 'em nice and re-usable, right?

Please, don't. https://github.com/styleguide/templates/2.0

-->
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>Unicorn! &middot; GitHub</title>
    <style type="text/css" media="screen">
      body {
        background: #f1f1f1;
        font-family: "HelveticaNeue", Helvetica, Arial, sans-serif;
        text-rendering: optimizeLegibility;
        margin: 0; }

      .container { margin: 50px auto 40px auto; width: 600px; text-align: center; }

      a { color: #4183c4; text-decoration: none; }
      a:visited { color: #4183c4 }
      a:hover { text-decoration: none; }

      h1 { letter-spacing: -1px; line-height: 60px; font-size: 60px; font-weight: 100; margin: 0px; text-shadow: 0 1px 0 #fff; }
      p { color: rgba(0, 0, 0, 0.5); margin: 20px 0 40px; }

      ul { list-style: none; margin: 25px 0; padding: 0; }
      li { display: table-cell; font-weight: bold; width: 1%; }
      .divider { border-top: 1px solid #d5d5d5; border-bottom: 1px solid #fafafa;}


    </style>
  </head>
  <body>

    <div class="container">
      <p>
        <img src="data:image/png;base64,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">
      </p>

      <p><strong>This page is taking way too long to load.</strong></p>
      <p>Sorry about that. Please try refreshing and contact us if the problem persists.</p>
      <div class="divider"></div>
      <ul id="error-suggestions">
          <li><a href="https://github.com/contact">Contact Support</a></li>
          <li><a href="http://status.github.com">Status Site</a></li>
          <li><a href="http://twitter.com/github">@github</a></li>
      </ul>
      <p><img width="109" height="48" title="" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAG0AAAAwCAYAAAAb6PR/AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjdCQkMxMDhEQjI4MTFFMDk4REM4M0Q4MjE0NzE1RkUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NjdCQkMxMDlEQjI4MTFFMDk4REM4M0Q4MjE0NzE1RkUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyMjMxMjA2QkRCMjYxMUUwOThEQzgzRDgyMTQ3MTVGRSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyMjMxMjA2Q0RCMjYxMUUwOThEQzgzRDgyMTQ3MTVGRSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PhqQs8QAAArlSURBVHja7FwJY9q4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" /></p>
    </div>
  </body>
</html>

/* ==========================================================
 * bootstrap-affix.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#affix
 * ==========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* AFFIX CLASS DEFINITION
  * ====================== */

  var Affix = function (element, options) {
    this.options = $.extend({}, $.fn.affix.defaults, options)
    this.$window = $(window)
      .on('scroll.affix.data-api', $.proxy(this.checkPosition, this))
      .on('click.affix.data-api',  $.proxy(function () { setTimeout($.proxy(this.checkPosition, this), 1) }, this))
    this.$element = $(element)
    this.checkPosition()
  }

  Affix.prototype.checkPosition = function () {
    if (!this.$element.is(':visible')) return

    var scrollHeight = $(document).height()
      , scrollTop = this.$window.scrollTop()
      , position = this.$element.offset()
      , offset = this.options.offset
      , offsetBottom = offset.bottom
      , offsetTop = offset.top
      , reset = 'affix affix-top affix-bottom'
      , affix

    if (typeof offset != 'object') offsetBottom = offsetTop = offset
    if (typeof offsetTop == 'function') offsetTop = offset.top()
    if (typeof offsetBottom == 'function') offsetBottom = offset.bottom()

    affix = this.unpin != null && (scrollTop + this.unpin <= position.top) ?
      false    : offsetBottom != null && (position.top + this.$element.height() >= scrollHeight - offsetBottom) ?
      'bottom' : offsetTop != null && scrollTop <= offsetTop ?
      'top'    : false

    if (this.affixed === affix) return

    this.affixed = affix
    this.unpin = affix == 'bottom' ? position.top - scrollTop : null

    this.$element.removeClass(reset).addClass('affix' + (affix ? '-' + affix : ''))
  }


 /* AFFIX PLUGIN DEFINITION
  * ======================= */

  var old = $.fn.affix

  $.fn.affix = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('affix')
        , options = typeof option == 'object' && option
      if (!data) $this.data('affix', (data = new Affix(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.affix.Constructor = Affix

  $.fn.affix.defaults = {
    offset: 0
  }


 /* AFFIX NO CONFLICT
  * ================= */

  $.fn.affix.noConflict = function () {
    $.fn.affix = old
    return this
  }


 /* AFFIX DATA-API
  * ============== */

  $(window).on('load', function () {
    $('[data-spy="affix"]').each(function () {
      var $spy = $(this)
        , data = $spy.data()

      data.offset = data.offset || {}

      data.offsetBottom && (data.offset.bottom = data.offsetBottom)
      data.offsetTop && (data.offset.top = data.offsetTop)

      $spy.affix(data)
    })
  })


}(window.jQuery);
/* ==========================================================
 * bootstrap-alert.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#alerts
 * ==========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* ALERT CLASS DEFINITION
  * ====================== */

  var dismiss = '[data-dismiss="alert"]'
    , Alert = function (el) {
        $(el).on('click', dismiss, this.close)
      }

  Alert.prototype.close = function (e) {
    var $this = $(this)
      , selector = $this.attr('data-target')
      , $parent

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') //strip for ie7
    }

    $parent = $(selector)

    e && e.preventDefault()

    $parent.length || ($parent = $this.hasClass('alert') ? $this : $this.parent())

    $parent.trigger(e = $.Event('close'))

    if (e.isDefaultPrevented()) return

    $parent.removeClass('in')

    function removeElement() {
      $parent
        .trigger('closed')
        .remove()
    }

    $.support.transition && $parent.hasClass('fade') ?
      $parent.on($.support.transition.end, removeElement) :
      removeElement()
  }


 /* ALERT PLUGIN DEFINITION
  * ======================= */

  var old = $.fn.alert

  $.fn.alert = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('alert')
      if (!data) $this.data('alert', (data = new Alert(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  $.fn.alert.Constructor = Alert


 /* ALERT NO CONFLICT
  * ================= */

  $.fn.alert.noConflict = function () {
    $.fn.alert = old
    return this
  }


 /* ALERT DATA-API
  * ============== */

  $(document).on('click.alert.data-api', dismiss, Alert.prototype.close)

}(window.jQuery);
<!DOCTYPE html>
<!--

Hello future GitHubber! I bet you're here to remove those nasty inline styles,
DRY up these templates and make 'em nice and re-usable, right?

Please, don't. https://github.com/styleguide/templates/2.0

-->
<html>
  <head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>Unicorn! &middot; GitHub</title>
    <style type="text/css" media="screen">
      body {
        background: #f1f1f1;
        font-family: "HelveticaNeue", Helvetica, Arial, sans-serif;
        text-rendering: optimizeLegibility;
        margin: 0; }

      .container { margin: 50px auto 40px auto; width: 600px; text-align: center; }

      a { color: #4183c4; text-decoration: none; }
      a:visited { color: #4183c4 }
      a:hover { text-decoration: none; }

      h1 { letter-spacing: -1px; line-height: 60px; font-size: 60px; font-weight: 100; margin: 0px; text-shadow: 0 1px 0 #fff; }
      p { color: rgba(0, 0, 0, 0.5); margin: 20px 0 40px; }

      ul { list-style: none; margin: 25px 0; padding: 0; }
      li { display: table-cell; font-weight: bold; width: 1%; }
      .divider { border-top: 1px solid #d5d5d5; border-bottom: 1px solid #fafafa;}


    </style>
  </head>
  <body>

    <div class="container">
      <p>
        <img src="data:image/png;base64,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">
      </p>

      <p><strong>This page is taking way too long to load.</strong></p>
      <p>Sorry about that. Please try refreshing and contact us if the problem persists.</p>
      <div class="divider"></div>
      <ul id="error-suggestions">
          <li><a href="https://github.com/contact">Contact Support</a></li>
          <li><a href="http://status.github.com">Status Site</a></li>
          <li><a href="http://twitter.com/github">@github</a></li>
      </ul>
      <p><img width="109" height="48" title="" alt="" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAG0AAAAwCAYAAAAb6PR/AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBNYWNpbnRvc2giIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NjdCQkMxMDhEQjI4MTFFMDk4REM4M0Q4MjE0NzE1RkUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NjdCQkMxMDlEQjI4MTFFMDk4REM4M0Q4MjE0NzE1RkUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyMjMxMjA2QkRCMjYxMUUwOThEQzgzRDgyMTQ3MTVGRSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyMjMxMjA2Q0RCMjYxMUUwOThEQzgzRDgyMTQ3MTVGRSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PhqQs8QAAArlSURBVHja7FwJY9q4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" /></p>
    </div>
  </body>
</html>

/* =============================================================
 * bootstrap-collapse.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#collapse
 * =============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function ($) {

  "use strict"; // jshint ;_;


 /* COLLAPSE PUBLIC CLASS DEFINITION
  * ================================ */

  var Collapse = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.collapse.defaults, options)

    if (this.options.parent) {
      this.$parent = $(this.options.parent)
    }

    this.options.toggle && this.toggle()
  }

  Collapse.prototype = {

    constructor: Collapse

  , dimension: function () {
      var hasWidth = this.$element.hasClass('width')
      return hasWidth ? 'width' : 'height'
    }

  , show: function () {
      var dimension
        , scroll
        , actives
        , hasData

      if (this.transitioning) return

      dimension = this.dimension()
      scroll = $.camelCase(['scroll', dimension].join('-'))
      actives = this.$parent && this.$parent.find('> .accordion-group > .in')

      if (actives && actives.length) {
        hasData = actives.data('collapse')
        if (hasData && hasData.transitioning) return
        actives.collapse('hide')
        hasData || actives.data('collapse', null)
      }

      this.$element[dimension](0)
      this.transition('addClass', $.Event('show'), 'shown')
      $.support.transition && this.$element[dimension](this.$element[0][scroll])
    }

  , hide: function () {
      var dimension
      if (this.transitioning) return
      dimension = this.dimension()
      this.reset(this.$element[dimension]())
      this.transition('removeClass', $.Event('hide'), 'hidden')
      this.$element[dimension](0)
    }

  , reset: function (size) {
      var dimension = this.dimension()

      this.$element
        .removeClass('collapse')
        [dimension](size || 'auto')
        [0].offsetWidth

      this.$element[size !== null ? 'addClass' : 'removeClass']('collapse')

      return this
    }

  , transition: function (method, startEvent, completeEvent) {
      var that = this
        , complete = function () {
            if (startEvent.type == 'show') that.reset()
            that.transitioning = 0
            that.$element.trigger(completeEvent)
          }

      this.$element.trigger(startEvent)

      if (startEvent.isDefaultPrevented()) return

      this.transitioning = 1

      this.$element[method]('in')

      $.support.transition && this.$element.hasClass('collapse') ?
        this.$element.one($.support.transition.end, complete) :
        complete()
    }

  , toggle: function () {
      this[this.$element.hasClass('in') ? 'hide' : 'show']()
    }

  }


 /* COLLAPSE PLUGIN DEFINITION
  * ========================== */

  var old = $.fn.collapse

  $.fn.collapse = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('collapse')
        , options = typeof option == 'object' && option
      if (!data) $this.data('collapse', (data = new Collapse(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.collapse.defaults = {
    toggle: true
  }

  $.fn.collapse.Constructor = Collapse


 /* COLLAPSE NO CONFLICT
  * ==================== */

  $.fn.collapse.noConflict = function () {
    $.fn.collapse = old
    return this
  }


 /* COLLAPSE DATA-API
  * ================= */

  $(document).on('click.collapse.data-api', '[data-toggle=collapse]', function (e) {
    var $this = $(this), href
      , target = $this.attr('data-target')
        || e.preventDefault()
        || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '') //strip for ie7
      , option = $(target).data('collapse') ? 'toggle' : $this.data()
    $this[$(target).hasClass('in') ? 'addClass' : 'removeClass']('collapsed')
    $(target).collapse(option)
  })

}(window.jQuery);
/* ==========================================================
 * bootstrap-carousel.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#carousel
 * ==========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function ($) {

  "use strict"; // jshint ;_;


 /* CAROUSEL CLASS DEFINITION
  * ========================= */

  var Carousel = function (element, options) {
    this.$element = $(element)
    this.options = options
    this.options.pause == 'hover' && this.$element
      .on('mouseenter', $.proxy(this.pause, this))
      .on('mouseleave', $.proxy(this.cycle, this))
  }

  Carousel.prototype = {

    cycle: function (e) {
      if (!e) this.paused = false
      this.options.interval
        && !this.paused
        && (this.interval = setInterval($.proxy(this.next, this), this.options.interval))
      return this
    }

  , to: function (pos) {
      var $active = this.$element.find('.item.active')
        , children = $active.parent().children()
        , activePos = children.index($active)
        , that = this

      if (pos > (children.length - 1) || pos < 0) return

      if (this.sliding) {
        return this.$element.one('slid', function () {
          that.to(pos)
        })
      }

      if (activePos == pos) {
        return this.pause().cycle()
      }

      return this.slide(pos > activePos ? 'next' : 'prev', $(children[pos]))
    }

  , pause: function (e) {
      if (!e) this.paused = true
      if (this.$element.find('.next, .prev').length && $.support.transition.end) {
        this.$element.trigger($.support.transition.end)
        this.cycle()
      }
      clearInterval(this.interval)
      this.interval = null
      return this
    }

  , next: function () {
      if (this.sliding) return
      return this.slide('next')
    }

  , prev: function () {
      if (this.sliding) return
      return this.slide('prev')
    }

  , slide: function (type, next) {
      var $active = this.$element.find('.item.active')
        , $next = next || $active[type]()
        , isCycling = this.interval
        , direction = type == 'next' ? 'left' : 'right'
        , fallback  = type == 'next' ? 'first' : 'last'
        , that = this
        , e

      this.sliding = true

      isCycling && this.pause()

      $next = $next.length ? $next : this.$element.find('.item')[fallback]()

      e = $.Event('slide', {
        relatedTarget: $next[0]
      })

      if ($next.hasClass('active')) return

      if ($.support.transition && this.$element.hasClass('slide')) {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $next.addClass(type)
        $next[0].offsetWidth // force reflow
        $active.addClass(direction)
        $next.addClass(direction)
        this.$element.one($.support.transition.end, function () {
          $next.removeClass([type, direction].join(' ')).addClass('active')
          $active.removeClass(['active', direction].join(' '))
          that.sliding = false
          setTimeout(function () { that.$element.trigger('slid') }, 0)
        })
      } else {
        this.$element.trigger(e)
        if (e.isDefaultPrevented()) return
        $active.removeClass('active')
        $next.addClass('active')
        this.sliding = false
        this.$element.trigger('slid')
      }

      isCycling && this.cycle()

      return this
    }

  }


 /* CAROUSEL PLUGIN DEFINITION
  * ========================== */

  var old = $.fn.carousel

  $.fn.carousel = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('carousel')
        , options = $.extend({}, $.fn.carousel.defaults, typeof option == 'object' && option)
        , action = typeof option == 'string' ? option : options.slide
      if (!data) $this.data('carousel', (data = new Carousel(this, options)))
      if (typeof option == 'number') data.to(option)
      else if (action) data[action]()
      else if (options.interval) data.cycle()
    })
  }

  $.fn.carousel.defaults = {
    interval: 5000
  , pause: 'hover'
  }

  $.fn.carousel.Constructor = Carousel


 /* CAROUSEL NO CONFLICT
  * ==================== */

  $.fn.carousel.noConflict = function () {
    $.fn.carousel = old
    return this
  }

 /* CAROUSEL DATA-API
  * ================= */

  $(document).on('click.carousel.data-api', '[data-slide]', function (e) {
    var $this = $(this), href
      , $target = $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')) //strip for ie7
      , options = $.extend({}, $target.data(), $this.data())
    $target.carousel(options)
    e.preventDefault()
  })

}(window.jQuery);
/* =============================================================
 * bootstrap-typeahead.js v2.2.2
 * http://twitter.github.com/bootstrap/javascript.html#typeahead
 * =============================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */


!function($){

  "use strict"; // jshint ;_;


 /* TYPEAHEAD PUBLIC CLASS DEFINITION
  * ================================= */

  var Typeahead = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, $.fn.typeahead.defaults, options)
    this.matcher = this.options.matcher || this.matcher
    this.sorter = this.options.sorter || this.sorter
    this.highlighter = this.options.highlighter || this.highlighter
    this.updater = this.options.updater || this.updater
    this.source = this.options.source
    this.$menu = $(this.options.menu)
    this.shown = false
    this.listen()
  }

  Typeahead.prototype = {

    constructor: Typeahead

  , select: function () {
      var val = this.$menu.find('.active').attr('data-value')
      this.$element
        .val(this.updater(val))
        .change()
      return this.hide()
    }

  , updater: function (item) {
      return item
    }

  , show: function () {
      var pos = $.extend({}, this.$element.position(), {
        height: this.$element[0].offsetHeight
      })

      this.$menu
        .insertAfter(this.$element)
        .css({
          top: pos.top + pos.height
        , left: pos.left
        })
        .show()

      this.shown = true
      return this
    }

  , hide: function () {
      this.$menu.hide()
      this.shown = false
      return this
    }

  , lookup: function (event) {
      var items

      this.query = this.$element.val()

      if (!this.query || this.query.length < this.options.minLength) {
        return this.shown ? this.hide() : this
      }

      items = $.isFunction(this.source) ? this.source(this.query, $.proxy(this.process, this)) : this.source

      return items ? this.process(items) : this
    }

  , process: function (items) {
      var that = this

      items = $.grep(items, function (item) {
        return that.matcher(item)
      })

      items = this.sorter(items)

      if (!items.length) {
        return this.shown ? this.hide() : this
      }

      return this.render(items.slice(0, this.options.items)).show()
    }

  , matcher: function (item) {
      return ~item.toLowerCase().indexOf(this.query.toLowerCase())
    }

  , sorter: function (items) {
      var beginswith = []
        , caseSensitive = []
        , caseInsensitive = []
        , item

      while (item = items.shift()) {
        if (!item.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(item)
        else if (~item.indexOf(this.query)) caseSensitive.push(item)
        else caseInsensitive.push(item)
      }

      return beginswith.concat(caseSensitive, caseInsensitive)
    }

  , highlighter: function (item) {
      var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
      return item.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
        return '<strong>' + match + '</strong>'
      })
    }

  , render: function (items) {
      var that = this

      items = $(items).map(function (i, item) {
        i = $(that.options.item).attr('data-value', item)
        i.find('a').html(that.highlighter(item))
        return i[0]
      })

      items.first().addClass('active')
      this.$menu.html(items)
      return this
    }

  , next: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , next = active.next()

      if (!next.length) {
        next = $(this.$menu.find('li')[0])
      }

      next.addClass('active')
    }

  , prev: function (event) {
      var active = this.$menu.find('.active').removeClass('active')
        , prev = active.prev()

      if (!prev.length) {
        prev = this.$menu.find('li').last()
      }

      prev.addClass('active')
    }

  , listen: function () {
      this.$element
        .on('blur',     $.proxy(this.blur, this))
        .on('keypress', $.proxy(this.keypress, this))
        .on('keyup',    $.proxy(this.keyup, this))

      if (this.eventSupported('keydown')) {
        this.$element.on('keydown', $.proxy(this.keydown, this))
      }

      this.$menu
        .on('click', $.proxy(this.click, this))
        .on('mouseenter', 'li', $.proxy(this.mouseenter, this))
    }

  , eventSupported: function(eventName) {
      var isSupported = eventName in this.$element
      if (!isSupported) {
        this.$element.setAttribute(eventName, 'return;')
        isSupported = typeof this.$element[eventName] === 'function'
      }
      return isSupported
    }

  , move: function (e) {
      if (!this.shown) return

      switch(e.keyCode) {
        case 9: // tab
        case 13: // enter
        case 27: // escape
          e.preventDefault()
          break

        case 38: // up arrow
          e.preventDefault()
          this.prev()
          break

        case 40: // down arrow
          e.preventDefault()
          this.next()
          break
      }

      e.stopPropagation()
    }

  , keydown: function (e) {
      this.suppressKeyPressRepeat = ~$.inArray(e.keyCode, [40,38,9,13,27])
      this.move(e)
    }

  , keypress: function (e) {
      if (this.suppressKeyPressRepeat) return
      this.move(e)
    }

  , keyup: function (e) {
      switch(e.keyCode) {
        case 40: // down arrow
        case 38: // up arrow
        case 16: // shift
        case 17: // ctrl
        case 18: // alt
          break

        case 9: // tab
        case 13: // enter
          if (!this.shown) return
          this.select()
          break

        case 27: // escape
          if (!this.shown) return
          this.hide()
          break

        default:
          this.lookup()
      }

      e.stopPropagation()
      e.preventDefault()
  }

  , blur: function (e) {
      var that = this
      setTimeout(function () { that.hide() }, 150)
    }

  , click: function (e) {
      e.stopPropagation()
      e.preventDefault()
      this.select()
    }

  , mouseenter: function (e) {
      this.$menu.find('.active').removeClass('active')
      $(e.currentTarget).addClass('active')
    }

  }


  /* TYPEAHEAD PLUGIN DEFINITION
   * =========================== */

  var old = $.fn.typeahead

  $.fn.typeahead = function (option) {
    return this.each(function () {
      var $this = $(this)
        , data = $this.data('typeahead')
        , options = typeof option == 'object' && option
      if (!data) $this.data('typeahead', (data = new Typeahead(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  $.fn.typeahead.defaults = {
    source: []
  , items: 8
  , menu: '<ul class="typeahead dropdown-menu"></ul>'
  , item: '<li><a href="#"></a></li>'
  , minLength: 1
  }

  $.fn.typeahead.Constructor = Typeahead


 /* TYPEAHEAD NO CONFLICT
  * =================== */

  $.fn.typeahead.noConflict = function () {
    $.fn.typeahead = old
    return this
  }


 /* TYPEAHEAD DATA-API
  * ================== */

  $(document).on('focus.typeahead.data-api', '[data-provide="typeahead"]', function (e) {
    var $this = $(this)
    if ($this.data('typeahead')) return
    e.preventDefault()
    $this.typeahead($this.data())
  })

}(window.jQuery);
