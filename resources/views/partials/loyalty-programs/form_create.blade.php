<div class="box-body">
	<div class="form-group">
		<div class="radio">
			<label>
				<input type="radio" name="optionsRadios" id="increments" value="increments" v-model="selection">
				Reward customers once they make a certain number of purchases
			</label>
		</div>
		<div class="radio">
			<label>
				<input type="radio" name="optionsRadios" id="amounts" value="amounts" v-model="selection">
				Or reward customers after they have spent a certain amount
			</label>
		</div>
	</div>
	<div class="form-group" v-if= "selection == 'increments'">
		<label for="purchases_required">Number of Purchases required for reward</label>
		<div class="input-group col-xs-3">
			<span class="input-group-addon">#</span>
			<input class="form-control" type="number" name="purchases_required" id="purchases_required" placeholder="20">
		</div>
	</div>
	<div class="form-group" v-if= "selection == 'amounts'">
		<label for="amount_required">Total amount customers must spend to receive reward</label>
		<div class="input-group col-xs-3">
			<span class="input-group-addon">$</span>
			<input class="form-control" type="number" name="amount_required" id="amount_required" pattern="^\\$?(([1-9](\\d*|\\d{0,2}(,\\d{3})*))|0)(\\.\\d{1,2})?$" step="any" placeholder="50.00">
		</div>
	</div>
</div>
<div class="box-footer">
  <button type="submit" class="btn btn-success">Creat</button>
</div>





