
<!DOCTYPE html>
<html>
<head>
	<title>Shopping list</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<style>
		#itemIndex{
			display:none;
		}
		#addRow{
			display:none;
		}
		.field{
			display:none;
		}
	</style>
	<script>
		jQuery.fn.outerHTML = function() {
		  return jQuery('<div />').append(this.eq(0).clone()).html();
		};
		$(function(){
			var editFunction = function(){
				$("#cancelAdd").click();
				$(".field").hide();
				$(".text").show();
				var row = $(this).closest("tr");
				row.find(".field").show();
				row.find(".text").hide();
			};
			var addOne = function(){
				var row = $(this).closest("tr");
				addToList(row.attr('id_food'), 1, false)
			};
			var addSpecific = function(){
				var amount = prompt("Please specify the amount to be added", 1);
				if(amount != 0 && amount != null && amount != ""){
					if($.isNumeric(amount)){
						var row = $(this).closest("tr");
						addToList(row.attr('id_food'), amount, false);
					}
					else{
						alert("Please enter a valid number");
						$(this).click();
					}
				}
			};
			var adjustAmount = function(){
				var amount = prompt("Please specify the amount to be set", 0);
				if(amount != 0 && amount != null && amount != ""){
					if($.isNumeric(amount)){
						var id_food = $(this).closest("li").attr('id_food');
						addToList(id_food, amount, true);
					}
					else{
						alert("Please enter a valid number");
						$(this).click();
					}
				}
			}
			var remove = function(){
				var id_food = $(this).closest("li").attr('id_food');
				addToList(id_food, 0, false);

			}
			var saveEdit = function(){
				saveItem($(this), false, true);
			};
			var cancelEdit = function(){
				var row = $(this).closest("tr");
				$(".text").show();
				row.find(".field").hide();
			};
			var searchFunction = function(){
		  		var searchTerm = $("#search").val();
		  		$("tbody tr").hide();
		  		
		  		//$("tbody tr:contains('"+searchTerm+"')").show();
		  		$(".food_name").filter(function(){
		  			if($(this).text().toLowerCase().search(searchTerm.toLowerCase()) === -1)
		  				return false
		  			else 
		  				return true; 
		  		}).closest('tr').show();
		  	};
			$("#toggleLists").click(function(){
				$("#shoppingList").toggle();
				$("#itemIndex").toggle();
				if($("#shoppingList").is(":visible")){
					$("#toggleLists").text("Show item index");
				}
				else{
					$("#toggleLists").text("Show shopping list");
				}
			});

			$("#search").keyup(searchFunction);

			$("#showAdd").click(function(){
				$("#addRow").show();
			});

			$("#saveAdd").click(function(){
				saveItem($(this), false, false);
			});

			$("#saveAddList").click(function(){
				saveItem($(this), true, false);
			});

			$("#cancelAdd").click(function(){
				$("#addRow").hide();
				$("#addRow :input").val("");
			});

			$(".addOne").click(addOne);

			$(".addSpecific").click(addSpecific);

			$(".adjust").click(adjustAmount);

			$(".remove").click(remove)

			$(".edit").click(editFunction);

			$(".saveEdit").click(saveEdit);

			$(".cancelEdit").click(cancelEdit);

			function saveItem(element, addToList, edit){
				var row = element.closest("tr");
				var name = row.find('.name').val();
				var category = row.find('.category').val();
				var categoryName = row.find('.category').find(":selected").text();
				if(edit){
					var id_food = row.attr('id_food')
				}
				else{
					var id_food = "";
				}
				var str = "name="+name+"&category="+category+"&addToList="+addToList+"&edit="+edit+"&id_food="+id_food;
	        	var $_token = $('[name="_token"]').val();
		  		$.ajax({
		            type: 'post',
		            cache: false,
		            @if(!array_key_exists("XSRF-TOKEN", $_COOKIE))
					<?php header("Refresh:0");
					//die(); ?>
					@else
		            headers: { 'X-XSRF-TOKEN' : "{{$_COOKIE['XSRF-TOKEN']}}" }, 
		            @endif
		            url: "{{URL::route('saveNewItem')}}",
		            data: str, 
		            dataType: 'json',
		            success: function(food_data){
		            	if(edit){
		            		row.find('.field').hide();
		            		row.find('.food_name').find('.text').text(name);
		            		row.find('.food_category').find('.text').text(categoryName);
		            		row.find('.food_name').find('.name').val(name);
		            		row.find('.food_category').find('.category').val(category);
		            		row.find('.text').show();

		            	}
		            	else{
		            		var addRow = "<tr id='index_"+food_data.id_food+"' id_food='"+food_data.id_food+"'>";
		            		addRow += "<td class='food_name'>";
		            		addRow += "<span class='text'>"+name+"</span>";
		            		addRow += "<span class='field'><input type='text' class='form-control name' value='"+name+"'></span></td>";
		            		addRow += "<td class='food_category' category_id='"+category+"'>";
		            		addRow += "<span class='text'>"+categoryName+"</span>";
		            		addRow += "<span class='field'>";
		            		addRow += "<select class='form-control category'>@foreach($categories as $category)<option value='{{$category->id_category}}'>{{$category->name}}</option>@endforeach</select></span></td>";
		            		addRow += "<td class='food_actions'>";
		            		addRow += "<span class='text'>"
		            		addRow += "<a href='#' class='addOne btn btn-primary'>Add 1</a>&nbsp;";
							addRow += "<a href='#' class=' addSpecific btn btn-primary'>Add X</a>&nbsp;";
							addRow += "<a href='#' class='edit btn btn-warning'>Edit</a></span>";
							addRow += "<span class='field'>";
							addRow += "<a href='#' class='btn btn-success saveEdit'>Save</a>&nbsp;";
							addRow += "<a href='#' class='btn btn-danger cancelEdit'>Cancel</a></span></td>";
							addRow += "</tr>";
		            		$("#itemIndexBody").append(addRow);
		            		$("#index_"+food_data.id_food).find(".addOne").click(addOne);
							$("#index_"+food_data.id_food).find(".addSpecific").click(addSpecific);
							$("#index_"+food_data.id_food).find(".edit").click(editFunction);
							$("#index_"+food_data.id_food).find(".saveEdit").click(saveEdit);
							$("#index_"+food_data.id_food).find(".cancelEdit").click(cancelEdit);
		            		$("#index_"+food_data.id_food).find(".category").val(category);
		            		if(addToList){
		            			$("#index_"+food_data.id_food).addClass("bg-success");
			            		$("#list_"+category).append("<li id='food_"+food_data.id_food+"'>1x "+name+" (<a href='#' class='adjust'>Adjust quantity</a>&nbsp;|&nbsp;<a href='#' class='remove'>Remove</a>)</li>");
			            		$("#category_"+category).show();
		            		}
		            		$("#cancelAdd").click();
		            	}
				  		
		            },
		            error: function(data){
		            	var errors = Object.values(JSON.parse(data.responseText).errors);
		                var errorsText = "";
		                errors.forEach(function(value, index){
		                    errorsText += "• "+value+"\n";
		                });
		                alert(errorsText);

				            }
		        });
			}

			function addToList(id_food, amount, adjust){
				var str = "id_food="+id_food+"&amount="+amount+"&adjust="+adjust;
		  		$.ajax({
		            type: 'post',
		            cache: false,
		            @if(!array_key_exists("XSRF-TOKEN", $_COOKIE))
					<?php header("Refresh:0");
					//die(); ?>
					@else
		            headers: { 'X-XSRF-TOKEN' : "{{$_COOKIE['XSRF-TOKEN']}}" }, 
		            @endif 
		            url: "{{URL::route('addToList')}}",
		            data: str, 
		            dataType: 'json',
		            success: function(food_data){
		            	if(amount > 0){
	            			$("#index_"+id_food).addClass("bg-success");
	            			if($("#food_"+id_food).length > 0){
			            		$("#food_"+id_food).html(food_data.quantity+"x "+food_data.name+" (<a href='#' class='adjust'>Adjust quantity</a>&nbsp;|&nbsp;<a href='#' class='remove'>Remove</a>)");
			            	}
			            	else{
			            		$("#list_"+food_data.category).append("<li id='food_"+id_food+"'>"+food_data.quantity+"x "+food_data.name+" (<a href='#' class='adjust'>Adjust quantity</a>&nbsp;|&nbsp;<a href='#' class='remove'>Remove</a>)</li>");
			            	}
			            	$("#food_"+id_food).find(".adjust").click(adjustAmount);
			            	$("#food_"+id_food).find(".remove").click(remove);
		            		$("#category_"+food_data.category).show();
		            		if(!adjust)
		            			alert("Added "+amount+" "+food_data.name+" to the shopping list");
	            		}
	            		else{
	            			$("#index_"+id_food).removeClass("bg-success");
	            			$("#food_"+id_food).remove();
	            			if ($("#category_"+food_data.category+" li").length == 0){
	            				$("#category_"+food_data.category).hide();
	            			}
	            		}
				  		
		            },
		            error: function(data){
		            	console.log(data);
		            	alert("Error");

		            }
		        });
			}
		});
	</script>
</head>
<body>
{{csrf_field()}}
	<a href="#" id="toggleLists">Show item index</a>
	<div id="shoppingList">
		<h1>Shopping list</h1>
		@foreach($categories as $category)
			<div id="category_{{$category->id_category}}"@if($category->foods()->whereNotNull("quantity")->first() === null) style="display:none" @endif>
				<h3>{{$category->name}}</h3>
				<ul id="list_{{$category->id_category}}">
					@foreach($category->foods()->whereNotNull("quantity")->get() as $food)
						<li id="food_{{$food->id_food}}" id_food="{{$food->id_food}}">{{$food->quantity}}x {{$food->name}} (<a href="#" class="adjust">Adjust quantity</a>&nbsp;|&nbsp;<a href="#" class="remove">Remove</a>)</li>
					@endforeach
				</ul>
			</div>
		@endforeach
	</div>
	<div id="itemIndex">
		<h1>Item index</h1>
		<input type="text" class="form-control" id="search" placeholder="Search">
		<a href="#" id="showAdd" class="btn btn-primary">Add item</a><br><br>
		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Name</th>
					<th>Category</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tr id="addRow">
				<td><input type="text" class="form-control name" placeholder="Name"></td>
				<td>
					<select class="form-control category">
						<option value="">Please select</option>
						@foreach($categories as $category)
							<option value="{{$category->id_category}}">{{$category->name}}</option>
						@endforeach
					</select>
				</td>
				<td>
					<a href="#" id="saveAdd" class="btn btn-success">Add to index</a>
					<a href="#" id="saveAddList" class="btn btn-primary">Add to index and shopping list</a>
					<a href="#" id="cancelAdd" class="btn btn-danger">Cancel</a>
				</td>

			</tr>
			<tbody id="itemIndexBody">
				@foreach($foodItems as $foodItem)
					<tr id="index_{{$foodItem->id_food}}" id_food="{{$foodItem->id_food}}" @if($foodItem->quantity != null) class="bg-success" @endif>
						<td class="food_name">
							<span class="text">{{$foodItem->name}}</span>
							<span class="field"><input type="text" class="form-control name" value="{{$foodItem->name}}"></span>
						</td>
						<td class="food_category" id_category="{{$foodItem->id_category}}">
							<span class="text">@if($foodItem->category !== null){{$foodItem->category->name}}@endif</span>
							<span class="field">
								<select class="form-control category">
									@foreach($categories as $category)
										<option @if($category->id_category == $foodItem->id_category) selected @endif value="{{$category->id_category}}">{{$category->name}}</option>
									@endforeach
								</select>
							</span>
						</td>
						<td class="food_actions">
							<span class="text">
								<a href="#" class="addOne btn btn-primary">Add 1</a>
								<a href="#" class="addSpecific btn btn-primary">Add X</a>
								<a href="#" class="edit btn btn-warning">Edit</a>
							</span>
							<span class="field">
								<a href='#' class='btn btn-success saveEdit'>Save</a>
								<a href='#' class='btn btn-danger cancelEdit'>Cancel</a>
							</span>
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</body>