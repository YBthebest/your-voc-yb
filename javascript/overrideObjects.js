Array.prototype.indexOf = function(obj){
	var l = this.length;
	for(var i=0; i<l; i++){
		if(this[i] == obj){
			return i;
		}
	}
	return -1;
};

Array.prototype.remove = function(obj){
	var l = this.length;
	var replace = false;
	for(var i=0; i<l; i++){
		if(this[i] == obj){
			replace = true;
		}
		if(replace && i<l){
			this[i] = this[i+1];
		}
	}
	if(replace){
		this.pop();
	}
};

Array.prototype.insert = function(p_index, newObj){
	var l = this.length;
	var precObj = null;
	for(var i=0; i<l; i++){
		if(precObj != null){
			var obj = precObj;
			precObj = this[i];
			this[i] = obj;
		}else if(i == p_index){
			precObj = this[i];
			this[i] = newObj;
		}
	}
	if(precObj != null){
		this.push(precObj);
	}else{
		this.push(newObj);
	}
};

Array.prototype.sortByProperties = function(properties){
	
};