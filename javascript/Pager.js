/*
 * listeDefintion : Object {}
 * attributes required : liste, 
 * attributes optional : nbPerPage, idListeContainer, classPagerContainer, containerListeCreator, elementCreator, pageChanger
 */
Pager = function(listeDefintion) {		
	this.liste = listeDefintion.liste;
	this.nbPerPage = (listeDefintion.nbPerPage)?listeDefintion.nbPerPage:20;
	this.currentPage = 0;
	this.nbPage = getNbPage(this.liste, this.nbPerPage);
	this.idContainerListe = this.initContainerListe(listeDefintion.idListeContainer);	
	this.$containersPager = this.initContainerPager(listeDefintion.classPagerContainer);
	this.containerListeCreator = (listeDefintion.containerListeCreator)?listeDefintion.containerListeCreator:this.defaultContainerListeCreator;
	this.elementCreator = (listeDefintion.elementCreator)?listeDefintion.elementCreator:this.defaultElementCreator;
	this.pageChanger = (listeDefintion.pageChanger)?listeDefintion.pageChanger:this.defaultPageChanger;
	this.defaultSelectorStyle = "list-style:none;float:left; text-align:center; margin:2px; cursor:pointer; height:20px; color:#be3737";	
	this.addPagerContainer();
	this.select(1);
};

Pager.prototype.initContainerListe = function (id){
	var idContainer = (id)?id:"idPagerListeContainer";
	containerListe = $("#"+idContainer);
	if(containerListe.length == 0){
		containerListe = $('<div id="'+idContainer+'" />');
		$('body').append(containerListe);
	}
	return idContainer;
};

Pager.prototype.initContainerPager = function (classe){
	var classPager = (classe)?classe:"classPagerContainer";
	containersPager = $("."+classPager);
	if(containersPager.length == 0 &&  this.nbPage > 1){
		containersPager = $('<div class="'+classPager+'" />');
		$("#"+this.idContainerListe).before(containersPager);
	}
	return containersPager;
};

Pager.prototype.addPagerContainer = function (){
	if(this.nbPage > 1){
		for(var i=0; i<this.$containersPager.length; i++){
			var $container = $('<ul id="pagineur'+i+'" style="height:60px;margin: 10px 0px;padding: 0px;"></ul>');
			this.initSelectors($container);
			$(this.$containersPager[i]).append($container);
			//this.select(this.currentPage);		
		}
	}
};

Pager.prototype.initSelectors = function($container){
	this.addSelector("<< ", "first", 1, $container);
	for(var i=1; i<= this.nbPage; i++){
		this.addSelector(i, "page"+i, i, $container);		
	}
	this.addSelector(" >>", "last", this.nbPage, $container);
};

Pager.prototype.select = function(index){
	if(this.nbPage > 1){
		for(var i=0; i<this.$containersPager.length; i++){
			var $parent = $(this.$containersPager[i]);
			var elemToSelect = $parent.find('.page'+index).get(0);
			if(this.currentPage != index){
				var elemSelected = $parent.find('.page'+this.currentPage).get(0);
				this.unSelectElem(elemSelected);		
				if(index == 1){
					this.selectElem($parent.find('.first').get(0));	
				}else if(this.currentPage == 1){
					this.unSelectElem($parent.find('.first').get(0));
				}
				if(index == this.nbPage){
					this.selectElem($parent.find('.last').get(0));	
				}else if(this.currentPage == this.nbPage){
					this.unSelectElem($parent.find('.last').get(0));
				}
			}
			elemToSelect.innerHTML = "[ "+elemToSelect.texte+" ]";		
			this.selectElem(elemToSelect);
			
		}
	}
	this.currentPage = index;
	this.displaySelectedPage();
};

Pager.prototype.unSelectElem = function(elem){
	if(elem){
		elem.innerHTML = elem.texte;
		elem.style.cssText = this.defaultSelectorStyle;	
		elem.isSelected = false;
	}
};

Pager.prototype.selectElem = function(elem){
	if(elem){
		elem.style.color = "white";
		elem.style.cursor = "";		
		elem.isSelected = true;
	}
};

Pager.prototype.addSelector = function(text, name, numPage, $parent){
	var pageSelector = createElem({tag:"li", className:name, texte:text, numPage:numPage});
	pageSelector.style.cssText = this.defaultSelectorStyle;
	pageSelector.appendChild(createElem({tag:"text", text:text}));
	var pager = this;
	pageSelector.onclick = function (){
		if(!this.isSelected){
			pager.select(this.numPage);			
			//pagineListesMot(page);
		}
	};
	$parent.append(pageSelector);
};

Pager.prototype.displaySelectedPage = function(){
	var start = (this.currentPage-1) * this.nbPerPage;
	var end = start + this.nbPerPage;
	if(start >= this.liste.length){
		end = this.liste.length-1;
	}
	this.pageChanger({listeObject:this.liste.slice(start, end), startIndex:start+1, elementCreator:this.elementCreator, idContainerListe:this.idContainerListe});
};

Pager.prototype.defaultContainerListeCreator = function (){
	alert("Creator container liste DOM not yet implemented");
};

Pager.prototype.defaultPageChanger = function(){
	alert("Creator page changer DOM not yet implemented");
};

Pager.prototype.defaultElementCreator = function(){
	alert("Creator Element DOM not yet implemented");
};