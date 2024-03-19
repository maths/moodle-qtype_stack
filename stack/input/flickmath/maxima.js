function tomaxima(elementID){
 //openmathのソースを取得しておく
	xx= document.getElementById(elementID).value;
	xmathml= org.mathdox.formulaeditor.FormulaEditor.getEditorByTextArea(elementID).getMathML();
 //かけ算の変換
	xtimes= xmathml.replace(/·/g, "&InvisibleTimes;");
 
 //2項以上のときに出る最初と最後の<mrow>タグを削除
	if(xx.match(/abs/)){
		trees= xtimes;
	}else{
		if(xtimes.match(/MathML\"><mrow>/)){
			trees= xtimes.replace("MathML\"><mrow>","MathML\">" );
		}else{
			trees= xtimes;
		}
	}
	//pc上では+によって項を区別しているので、x-yはx+(-y)の形に。
	tree0= trees.replace(/<mo>-<\/mo>/g, "<mo>+<\/mo><mo>-<\/mo>");
	

	if(tree0.match(/<mrow>/)){//<mrow>を含む
		ggg=tree0;//別文字に格納
		eme= tree0;

//分数
		var p0= ggg.match(/<mfrac>/g);//mfracの個数
		if(p0){//分数を含むとき
			i=0;
			kkk= ggg;
			var locstp0=[];
			var locfip0=[];
			var chaosp0= [];
			pw=0;
			fixep0= "";
			for(j=0; j< p0.length*2; j++){
				var st= ggg.match(/<mfrac>/);
				p0r1= RegExp.rightContext;//mfracの右
				p0l1= RegExp.leftContext;//mfracの左
				var fi= ggg.match(/<\/mfrac>/);
				p0r2= RegExp.rightContext;///mfracの右
				p0l2= RegExp.leftContext;///mfracの左
				var ttt= ggg.match(/<mfrac>|<\/mfrac>/);
				if(ttt){///mfracを１つ見つけたら
					if(ttt=="<mfrac>"){
						i=i+1;
						loc1= kkk.indexOf("<mfrac>",pw+1);//mfracが何文字目で始まるか
						if(i==1){
							yup01= p0l1;
							waitp01= loc1;
							locstp0.push(waitp01);//配列の後ろに追加していく
						}
						ggg=p0r1;
						pw= loc1;
					}else if(ttt=="<\/mfrac>"){
						i=i-1;//パラメータに-1
						loc2= kkk.indexOf("<\/mfrac>",pw+1);///mfracが何文字目で始まるか
						if(i==0){
							waitp02= loc2;
							locfip0.push(waitp02);//配列の後ろに追加していく
							chaosp0.push(kkk.substring(waitp01,waitp02+8));
							chaosp01= chaosp0.join();
							chaosp02= chaosp01.replace(/<mo>\+<\/mo>/g,"<mo>+¥<\/mo>");//一時的な置き換え
							chaosp0=[];
							fixp0= yup01+chaosp02;
							fixep0= fixep0+fixp0;
						}
						ggg= p0r2;///mfracの右側を検索させるため
						pw= loc2;
					}
				}
			}//for終わり	
			fixep0= fixep0+p0r2;
		}else{//分数を含まないとき
			fixep0= ggg;
		}
		
		
//指数
		var p1= fixep0.match(/<msup>/g);//msupの個数
		if(p1){//指数を含むとき
			i=0;
			kkk= fixep0;
			var locstp1=[];
			var locfip1=[];
			var chaosp1= [];
			pw=0;
			fixep1= "";
			
			for(j=0; j< p1.length*2; j++){
				var st= fixep0.match(/<msup>/);
				p1r1= RegExp.rightContext;//msupの右
				p1l1= RegExp.leftContext;//msupの左
				var fi= fixep0.match(/<\/msup>/);
				p1r2= RegExp.rightContext;///msupの右
				p1l2= RegExp.leftContext;///msupの左
				
				var ttt1= fixep0.match(/<msup>|<\/msup>/);
				if(ttt1){///msupを１つ見つけたら
					if(ttt1=="<msup>"){
						i=i+1;
						loc1= kkk.indexOf("<msup>",pw+1);//msupが何文字目で始まるか
						if(i==1){
							yup11= p1l1;
							waitp11= loc1;
							locstp1.push(waitp11);//配列の後ろに追加していくlocstにはmsupが何文字目から始まるか全て入っている
						}
						fixep0=p1r1;
						pw= loc1;
					
					}else if(ttt1=="<\/msup>"){
						i=i-1;//パラメータに-1
						loc2= kkk.indexOf("<\/msup>",pw+1);///msupが何文字目で始まるか
						if(i==0){
							waitp12= loc2;
							locfip1.push(waitp12);//配列の後ろに追加していく,locfiには/msupが何文字目から始まるか全て入っている
							chaosp1.push(kkk.substring(waitp11,waitp12+7));
							chaosp11= chaosp1.join();
							chaosp12= chaosp11.replace(/<mo>\+<\/mo>/g,"<mo>+¥<\/mo>");
							chaosp1=[];
							fixp1= yup11+chaosp12;
							fixep1= fixep1+fixp1;
						}
						fixep0= p1r2;///msupの右側を検索させるため
						pw= loc2;
						
					}
				}
			}//for終わり
			fixep1= fixep1+p1r2;
			
		}else{//指数を含まないとき
			fixep1= fixep0;
		}
		
		
//2乗根
		var p2= fixep1.match(/<msqrt>/g);//msqrtの個数
		if(p2){//乗根を含むとき
			i=0;
			kkk= fixep1;
			var locstp2=[];
			var locfip2=[];
			var chaosp2= [];
			pw=0;
			fixep2= "";
			
			for(j=0; j< p2.length*2; j++){
				var st= fixep1.match(/<msqrt>/);
				p2r1= RegExp.rightContext;//msqrtの右
				p2l1= RegExp.leftContext;//msqrtの左
				var fi= fixep1.match(/<\/msqrt>/);
				p2r2= RegExp.rightContext;///msqrtの右
				p2l2= RegExp.leftContext;///msqrtの左
				
				var ttt2= fixep1.match(/<msqrt>|<\/msqrt>/);
				if(ttt2){///msqrtを１つ見つけたら
					if(ttt2=="<msqrt>"){
						i=i+1;
						loc1= kkk.indexOf("<msqrt>",pw+1);//msqrtが何文字目で始まるか
						if(i==1){
							yup21= p2l1;
							waitp21= loc1;
							locstp2.push(waitp21);//配列の後ろに追加していくlocstにはmsqrtが何文字目から始まるか全て入っている
						}
						fixep1=p2r1;
						pw= loc1;
					
					}else if(ttt2=="<\/msqrt>"){
						i=i-1;//パラメータに-1
						loc2= kkk.indexOf("<\/msqrt>",pw+1);///msqrtが何文字目で始まるか
						if(i==0){
							waitp22= loc2;
							locfip2.push(waitp22);//配列の後ろに追加していく,locfiには/msqrtが何文字目から始まるか全て入っている
							chaosp2.push(kkk.substring(waitp21,waitp22+8));
							chaosp21= chaosp2.join();
							chaosp22= chaosp21.replace(/<mo>\+<\/mo>/g,"<mo>+¥<\/mo>");
							chaosp2=[];
							fixp2= yup21+chaosp22;
							fixep2= fixep2+fixp2;
						}
						fixep1= p2r2;///msqrtの右側を検索させるため
						pw= loc2;
						
					}
				}
			}//for終わり
			fixep2= fixep2+p2r2;
		
		}else{//乗根を含まないとき
			fixep2= fixep1;
		}	
		
		
//三乗根以上
		var p3= fixep2.match(/<mroot>/g);//msqrtの個数
		if(p3){//乗根を含むとき
			i=0;
			kkk= fixep2;
			var locstp3=[];
			var locfip3=[];
			var chaosp3= [];
			pw=0;
			fixep3= "";
			
			for(j=0; j< p3.length*2; j++){
				var st= fixep2.match(/<mroot>/);
				p3r1= RegExp.rightContext;//mrootの右
				p3l1= RegExp.leftContext;//mrootの左
				var fi= fixep2.match(/<\/mroot>/);
				p3r2= RegExp.rightContext;///mrootの右
				p3l2= RegExp.leftContext;///mrootの左
				
				var ttt3= fixep2.match(/<mroot>|<\/mroot>/);
				if(ttt3){///mrootを１つ見つけたら
					if(ttt3=="<mroot>"){
						i=i+1;
						loc1= kkk.indexOf("<mroot>",pw+1);//mrootが何文字目で始まるか
						if(i==1){
							yup31= p3l1;
							waitp31= loc1;
							locstp3.push(waitp31);//配列の後ろに追加していくlocstにはmrootが何文字目から始まるか全て入っている
						}
						fixep2=p3r1;
						pw= loc1;
					
					}else if(ttt3=="<\/mroot>"){
						i=i-1;//パラメータに-1
						loc2= kkk.indexOf("<\/mroot>",pw+1);///mrootが何文字目で始まるか
						if(i==0){
							waitp32= loc2;
							locfip3.push(waitp32);//配列の後ろに追加していく,locfiには/mrootが何文字目から始まるか全て入っている
							chaosp3.push(kkk.substring(waitp31,waitp32+8));
							chaosp31= chaosp3.join();
							chaosp32= chaosp31.replace(/<mo>\+<\/mo>/g,"<mo>+¥<\/mo>");
							chaosp3=[];
							fixp3= yup31+chaosp32;
							fixep3= fixep3+fixp3;
						}
						fixep2= p3r2;///mrootの右側を検索させるため
						pw= loc2;
						
					}
				}
			}//for終わり
			fixep3= fixep3+p3r2;
		
		}else{//乗根を含まないとき
			fixep3= fixep2;
		}
		
		
//行列
		var p4= fixep3.match(/<mtable>/g);//mtableの個数
		if(p4){//乗根を含むとき
			i=0;
			kkk= fixep3;
			var locstp4=[];
			var locfip4=[];
			var chaosp4= [];
			pw=0;
			fixep4= "";
			
			for(j=0; j< p4.length*2; j++){
				var st= fixep3.match(/<mtable>/);
				p4r1= RegExp.rightContext;//mtableの右
				p4l1= RegExp.leftContext;//mtableの左
				var fi= fixep3.match(/<\/mtable>/);
				p4r2= RegExp.rightContext;///mtableの右
				p4l2= RegExp.leftContext;///mtableの左
				
				var ttt4= fixep3.match(/<mtable>|<\/mtable>/);
				if(ttt4){///mtableを１つ見つけたら
					if(ttt4=="<mtable>"){
						i=i+1;
						loc1= kkk.indexOf("<mtable>",pw+1);//mtableが何文字目で始まるか
						if(i==1){
							yup41= p4l1;
							waitp41= loc1;
							locstp4.push(waitp41);//配列の後ろに追加していくlocstにはmtableが何文字目から始まるか全て入っている
						}
						fixep3=p4r1;
						pw= loc1;
					
					}else if(ttt4=="<\/mtable>"){
						i=i-1;//パラメータに-1
						loc2= kkk.indexOf("<\/mtable>",pw+1);///mtableが何文字目で始まるか
						if(i==0){
							waitp42= loc2;
							locfip4.push(waitp42);//配列の後ろに追加していく,locfiには/mtableが何文字目から始まるか全て入っている
							chaosp4.push(kkk.substring(waitp41,waitp42+9));
							chaosp41= chaosp4.join();
							chaosp42= chaosp41.replace(/<mo>\+<\/mo>/g,"<mo>+¥<\/mo>");
							chaosp4=[];
							fixp4= yup41+chaosp42;
							fixep4= fixep4+fixp4;
						}
						fixep3= p4r2;///mtableの右側を検索させるため
						pw= loc2;
						
					}
				}
			}//for終わり
			fixep4= fixep4+p4r2;
		
		}else{//行列を含まないとき
			fixep4= fixep3;
		}
		

//括弧
		var p5= fixep4.match(/<mo>\(<\/mo>/g);//括弧の個数
		if(p5){//乗根を含むとき
			i=0;
			kkk= fixep4;
			var locstp5=[];
			var locfip5=[];
			var chaosp5= [];
			pw=0;
			fixep5= "";
			
			for(j=0; j< p5.length*2; j++){
				var st= fixep4.match(/<mo>\(<\/mo>/);
				p5r1= RegExp.rightContext;//括弧始まりの右
				p5l1= RegExp.leftContext;//括弧始まりの左
				var fi= fixep4.match(/<mo>\)<\/mo>/);
				p5r2= RegExp.rightContext;//括弧終わりの右
				p5l2= RegExp.leftContext;//括弧終わりの左
				
				var ttt5= fixep4.match(/<mo>\(<\/mo>|<mo>\)<\/mo>/);
				if(ttt5){//括弧始まりを１つ見つけたら
					if(ttt5=="<mo>\(<\/mo>"){
						i=i+1;
						loc1= kkk.indexOf("<mo>\(<\/mo>",pw+1);//括弧始まりが何文字目で始まるか
						if(i==1){
							yup51= p5l1;
							waitp51= loc1;
							locstp5.push(waitp51);//配列の後ろに追加していく
						}
						fixep4=p5r1;//右側を検索させるため
						pw= loc1;
					
					}else if(ttt5=="<mo>\)<\/mo>"){
						i=i-1;//パラメータに-1
						loc2= kkk.indexOf("<mo>\)<\/mo>",pw+1);//括弧終わりが何文字目で始まるか
						if(i==0){
							waitp52= loc2;
							locfip5.push(waitp52);//配列の後ろに追加していく
							chaosp5.push(kkk.substring(waitp51,waitp52+10));
							chaosp51= chaosp5.join();
							chaosp52= chaosp51.replace(/<mo>\+<\/mo>/g,"<mo>+¥<\/mo>");
							chaosp5=[];
							fixp5= yup51+chaosp52;
							fixep5= fixep5+fixp5;
						}
						
						fixep4= p5r2;//右側を検索させるため
						pw= loc2;
						
					}
				}
			}//for終わり
			fixep5= fixep5+p5r2;
			
		
		}else{//括弧を含まないとき
			fixep5= fixep4;
		}
		
		if(xx.match(/abs/)){//絶対値があったら項に分類できないため、一括で変換
			x2a= eme;
			Conversion();
			box= y1;
		}else{//絶対値がなければ、各項で変換
			
			tree0= fixep5;
			SplitConversion();
		}
		
	}else{//<mrow>を含まない
		
		SplitConversion();
	}
 
 //残ったタグを全部消す
	answer1= box.replace(/<[^<>]+>/g ,"");
 //（+-）となっている箇所の修正
	answer2= answer1.replace(/\+¥/g,"+").replace(/\+\-/g, "-");
 //&InvisibleTimes;表示を＊に変える
	ans= answer2.replace(/&InvisibleTimes;/g, "*");
	//最後に値の取得
	//document.getElementById("maximas").value = ans;
	ans = ans.replace(/\s+/g, "");
	return ans;
	
 }


//項にわけて処理する関数
 function SplitConversion(){
	//splitで各項を取り出す
		sp=tree0.split("<mo>+<\/mo>");
 
		box= "";
		for(i=0; i< sp.length; i=i+1){//sp[i]がそれぞれ項にあたる
		
			x2a= sp[i];//それぞれ項をx2aに入れる
			Conversion();//一連の処理
			
			if(sp[i+1]==null){//後ろに符号がないとき
				se= y1;
			}else{//後ろに符号があれば符号も加える
				se= y1+"+";
			}
			box= box+se;//各項を処理した後並べる
		}
 }

//一連の関数
 function Conversion(){
 //指数関数
	if(x2a.match(/(<msup>)/g)){
		Exponential();
	}else{
		x4= x2a;//指数関数でない
	}
 
 //乗根で入力されたとき
	if(x4.match(/(<msqrt>)/g)){//√
		xr= x4.replace(/<msqrt>/g, "<msqrt>sqrt(").replace(/<\/msqrt>/g, ")<\/msqrt>");
	}else{
		xr= x4;//乗根でない
	}
 
 //三乗根以上
	if(xr.match(/(<mroot>)/g)){
		Root();
	}else{
		xroot= xr;//乗根でない
	}
 
 //分数
	if(xroot.match(/(<mfrac>)/g)){
		Fraction();
	}else{
		x5= xroot;//分数でない
	}
 
 //自然対数
	if(x5.match(/<mi>ln<\/mi>/)){
		xlog1= x5.replace(/<mi>ln<\/mi>/g, "log");
	}else{
		xlog1= x5;//自然対数でない
	}
 
 //対数
	if(xlog1.match(/<mi>log<\/mi>/g)){
		Logarithm();
	}else{
		xloga= xlog1;//自然対数でない
	}
	
 //行列
	if(xloga.match(/(<mtable>)/g)){
		Matrix();
	}else if(xx.match(/vector/)){//列ベクトルになっている場合
		
		//特定文字の左側取得
		var rex= new RegExp(",", "i");
		if(xloga.match(rex)){
			tb3=RegExp.leftContext+"],[";//特定文字の左側取得
			tb4=RegExp.rightContext;//特定文字の右側取得
			if(tb4.match(rex)){//三次元か確認
				tb4= tb4.replace(",", "],[");
			}
		}
		
		tb= "matrix"+"("+tb3+tb4.replace("<\/mrow><\/math>", ")");
		
	}else{
		tb= xloga;//行列でないとき
	}
 
 //πの処理
	if(tb.match(/π/)){
		z= tb.replace(/π/g, "%pi");
	}else{
		z= tb;
	}
	
 //三角関数(累乗)の処理
	Trigonometric();
	
 //絶対値
	if(xx.match(/abs/)){
		y= "abs" + ztan.replace(/<mrow><mo>\|<\/mo>/g, "(").replace(/<mo>\|<\/mo><\/mrow>/g, ")");
	}else{
		y= ztan;//絶対値でない
	}
 //不等号の変換
	y1= y.replace(/≤/g,"<=").replace(/≥/g,">=");

 }


//指数関数
 function Expo1(){
		zzz = xexpo;
			if(zzz.match(/(<msup><mrow>)/g)){//底が多項式
				x3a=zzz.replace(/(<\/mrow><mrow>)/g , "<\/mrow>^(<mrow>" ).replace(/(<\/mrow><mi>)/g , "<\/mrow>^<mi>").replace(/(<\/mrow><mn>)/g , "<\/mrow>^<mn>" ).replace(/(<\/mrow><\/msup>)/g, "<\/mrow>)<\/msup>");
			}else{
				x3a = zzz;
			}
			if(x3a.match(/(<msup><mi>)/g)){//底が文字
				
				if(x3a.match(/(<msup><mi>e<\/mi>)/g)){//底がネピアのe
					if(x3a.match(/(<msup><mi>e<\/mi><mn>)/g)){//指数が数字
						xemn= x3a.replace(/(<mi>e<\/mi><mn>)/g, "exp(" ).replace(/(<\/mn><\/msup>)/g , ")<\/msup>");
					}else{
						xemn= x3a;
					}
					if(xemn.match(/(<msup><mi>e<\/mi><mi>)/g)){//指数が文字
						xemi= xemn.replace(/(<mi>e<\/mi><mi>)/g, "exp(" ).replace(/(<\/mi><\/msup>)/g , ")<\/msup>");
					}else{
						xemi= xemn;
					}
					if(xemi.match(/(<msup><mi>e<\/mi><mrow>)/g)){//指数が式
						xe= xemi.replace(/(<mi>e<\/mi><mrow>)/g , "exp(").replace(/(<\/mrow><\/msup>)/g , ")<\/msup>");
					}else{
						xe= xemi;
					}
				}else{
					xe= x3a;
				}
				
				if(xe.match(/(<\/mfrac><\/msup>)/g)){//底が文字かつ指数が分数
					x3bbe =xe.replace(/(<\/mi><mfrac>)/g, "^(<mfrac>" ).replace(/(<\/msup>)/g , ")<\/msup>" );//mfracは下で使うため残す
					if(x3bbe.match(/(<mi>e\^)/g)){//ネピアのeがあるとき
						x3bb=x3bbe.replace(/<mi>e\^/g,"exp");
					}else{
						x3bb= x3bbe;
					}
				}else{
					x3bb = xe.replace(/(<\/mi><mn>)/g, "^" ).replace(/(<\/mi><mi>)/g, "^" ).replace(/(<\/mi><mrow>)/g , "^(").replace(/(<\/mrow><\/msup>)/g , ")<\/msup>");
				}
				
				if(x3bb.match(/(<\/msup><\/msup>)/g)){//指数の中に指数
					x3b= x3bb.replace(/<\/mi><msup>/g, "^<\/mi><msup>");
				}else{
					x3b= x3bb;
				}
			}else{
				x3b= x3a;
			}
			if(x3b.match(/(<msup><mn>)/g)){//底が数字
				if(x3b.match(/(<\/mfrac><\/msup>)/g)){//底が数字かつ指数が分数
					x3c= x3b.replace(/(<\/mn><mfrac>)/g, "^(<mfrac>" ).replace(/(<\/msup>)/g, ")" );//mfracは下で使うため残す
				}else{
					x3c= x3b.replace(/(<\/mn><mi>)/g, "^" ).replace(/(<\/mn><mn>)/g, "^" ).replace(/(<\/mn><mrow>)/g, "^(").replace(/(<\/mrow><\/msup>)/g , ")<\/msup>");
				}
			
				if(x3c.match(/(<\/msup><\/msup>)/g)){//指数の中に指数
					x4z= x3c.replace(/<\/mn><msup>/g, "^<\/mn><msup>");
				}else{
					x4z= x3c;
				}
			}else{
				x4z= x3b;
			}
			exresult= x4z;
		
 }
 
 
 function Exponential(){
	if(x2a.match(/(<\/msup><\/msup>)/g)){//指数の中に指数
		x2be= x2a.replace(/<\/mi><msup>/g, "^(<\/mi><msup>").replace(/<\/mn><msup>/g, "^(<\/mn><msup>").replace(/<\/mrow><msup>/g, "^(<\/mrow><msup>").replace(/<\/msup><\/msup>/g, "<\/msup>)<\/msup>");
		
		if(x2be.match(/(<mi>e\^\(<\/mi>)/g)){//ネピアのe
			x2b= x2be.replace(/(<mi>e\^\(<\/mi>)/g,"exp(<\/mi>");
		}else{
			x2b= x2be;
		}
		var exmatch= x2b.match("<\/m.+><msup>.*?<\/msup>", "i");//最短一致
		zzz1= exmatch.join();//matchした配列の文字列化
		exmatchleft= RegExp.leftContext;
		exmatchright= RegExp.rightContext;
		xexpo= zzz1;
		Expo1();
		x4= exmatchleft+ exresult+ exmatchright;
		
			
	}else{//通常
		xexpo= x2a;
		Expo1();
		x4= exresult;
	}
 }

//三乗根以上
 function Root(){
		if(xr.match(/(<mroot><mrow>)/g)){//底が多項式
			xroot1= xr.replace( /(<mroot><mrow>)/g, "(").replace(/(<\/mrow><mn>)/g, ")^(1/").replace(/<\/mroot>/g, ")<\/mroot>");
		}else{
			xroot1 = xr;
		}
	
		if(xroot1.match(/(<mroot><mi>)/g)){//底が文字
			xroot2= xroot1.replace(/(<\/mi><mn>)/g, "^(1/").replace(/<\/mroot>/g, ")<\/mroot>");
		}else{
			xroot2= xroot1;
		}
		
		if(xroot2.match(/(<mroot><mn>)/g)){//底が数字
			xroot3= xroot2.replace(/(<\/mn><mn>)/g, "^(1/").replace(/<\/mroot>/g, ")<\/mroot>");
		}else{
			xroot3= xroot2;
		}
		
		if(xroot3.match(/(<mroot><mfrac>)/g)){//底が分数
			xroot= xroot3.replace(/(<mroot><mfrac>)/g, "<mroot>(<mfrac>").replace(/(<\/mfrac><mn>)/g, "<\/mfrac>)^(1/").replace(/<\/mroot>/g, ")<\/mroot>");
		}else{
			xroot= xroot3;
		}
 }
 
//分数
 function Fraction(){
		if(xroot.match(/<\/msup><\/mfrac>/g)){//分母に指数の単項式
			x4a= xroot.replace(/(<\/mn><msup>)/g, "<msup>/").replace(/(<\/mi><msup>)/g, "<msup>/").replace(/(<\/msup><msup>)/g, "<msup>/").replace(/(<\/msqrt><msup>)/g, "<msup>/").replace(/(<\/mroot><msup>)/g, "<msup>/");
		}else{
		x4a= xroot;
		}
		
		if(x4a.match(/<mfrac><mrow>/g)){//分子が多項式
			x4b= x4a.replace( /(<mfrac><mrow>)/g , "<mfrac>(<mrow>" ).replace( /(<\/mrow><mrow>)/g , "<\/mrow>)/(<mrow>").replace( /(<\/mrow><\/mfrac>)/g , "<\/mrow>)").replace( /(<\/mrow><mi>)/g, "<\/mrow>)/<mi>" ).replace(/(<\/mrow><mn>)/g, "<\/mrow>)/<mn>").replace(/<\/mrow><msup>/g, "<\/mrow>)/<msup>").replace(/<\/mrow><msqrt>/g, ")/").replace(/<\/mrow><mroot>/g, ")/");
		}else{
			x4b= x4a;
		}
		
		if (x4b.match(/(<mfrac><mn>)/g)){//分子が単項式かつ数字
			x4c= x4b.replace( /(<\/mn><mrow>)/g , "/(<mrow>" ).replace( /(<\/mrow><\/mfrac>)/g , "<\/mrow>)").replace( /(<\/mn><mi>)/g, "/").replace(/(<\/mn><mn>)/g , "/").replace(/(<\/mn><msup>)/g, "/").replace(/<\/mn><msqrt>/g, "/").replace(/<\/mn><mroot>/g, "/");
		}else{
			x4c= x4b;
		}
		
		if (x4c.match(/(<mfrac><mi>)/g)){//分子が単項式かつ文字
			x4d= x4c.replace( /(<\/mi><mrow>)/g , "/(<mrow>" ).replace( /(<\/mrow><\/mfrac>)/g , "<\/mrow>)").replace( /(<\/mi><mn>)/g , "/").replace( /(<\/mi><mi>)/g , "/").replace(/(<\/mi><msup>)/g, "/").replace(/<\/mi><msqrt>/g, "/").replace(/<\/mi><mroot>/g, "/");
		}else{
			x4d= x4c;
		}
		
		if(x4d.match(/(<mfrac><msup>)/g)){//分子に指数関数が含まれる
			x4e= x4d.replace(/(<\/msup><mi>)/g, "/" ).replace(/(<\/msup><mn>)/g, "/").replace(/(<\/msup><mrow>)/g, "/(" ).replace(/<\/mrow><\/mfrac>/g, ")" ).replace(/(<\/msup><msup>)/g, "/").replace(/(<\/msup><msqrt>)/g, "/").replace(/(<\/msup><mroot>)/g, "/");
		}else{
			x4e= x4d;
		}
	
		if(x4e.match(/(<mfrac><msqrt>)/g)){//分子が√だけの式
			x4f= x4e.replace(/(<\/msqrt><mn>)/g, "/").replace(/(<\/msqrt><mi>)/g, "/").replace(/(<\/msqrt><mrow>)/g, "/").replace(/(<\/msqrt><msqrt>)/g, "/").replace(/(<\/msqrt><mroot>)/g, "/");
		}else{
			x4f= x4e;
		}
		
		if(x4f.match(/(<mfrac><mroot>)/g)){//分子が√だけの式
			x5= x4f.replace(/(<\/mroot><mn>)/g, "/").replace(/(<\/mroot><mi>)/g, "/").replace(/(<\/mroot><mrow>)/g, "/").replace(/(<\/mroot><msqrt>)/g, "/").replace(/(<\/mroot><mroot>)/g, "/");
		}else{
			x5= x4f;
		}
 }
 
//対数
 function Logarithm(){
	bottomx= xlog1;
	var botx= bottomx.match(/<mo>\(<\/mo>(.+)<mo>,<\/mo>(.+)<mo>\)<\/mo>/);
	if(botx){
			
		bot00= RegExp.$1;
		bot11= RegExp.$2;
		res="log("+bot11+")"+"/"+"log("+bot00+")";
	}
	xloga= res;
	
 }
 
//行列
 function Matrix(){
		if(xx.match(/vector/)){//行列と列ベクトルが同時に存在するとき(かけ算)
		//特定文字の左側取得
			var tablev = new RegExp("&InvisibleTimes;", "i");
			if(xloga.match(tablev)){
				tablev1 = RegExp.leftContext;//特定文字の左側（行列）
				tvector1 = RegExp.rightContext;//特定文字の右側（ベクトル）
			}
			tablev2 = tablev1.replace(/<mtable>/g, "<mtable>matrix(" ).replace(/<\/mtable>/g, "])<\/mtable>" );
			tablev3 = tablev2.replace(/<mtr>/g,"<mtr>[").replace(/<\/mtd><mtd>/g, ",").replace(/<\/mtr><\/mrow><mrow><mtr>/g, "],");
		
		//特定文字の左側取得
			var rex= new RegExp(",", "i");
			if(tvector1.match(rex)){
			tvector2 =RegExp.leftContext+"],[";//特定文字の左側取得
			tvector3 =RegExp.rightContext;//特定文字の右側取得
			
				if(tvector3.match(rex)){//三次元か確認
					tvector3= tvector3.replace(",", "],[");
				}
			
			tvector4= "matrix"+"("+tvector2+tvector3.replace("<\/mrow><\/math>", ")");
			tb= tablev3+ "."+ tvector4;//行列＊列ベクトル
			}
		}else{
		
			tb1= xloga.replace(/<mtable>/g, "<mtable>matrix(" ).replace(/<\/mtable>/g, "])<\/mtable>" );
			tb2= tb1.replace(/<mtr>/g,"<mtr>[").replace(/<\/mtd><mtd>/g, ",").replace(/<\/mtr><\/mrow><mrow><mtr>/g, "],");
			tb= tb2.replace(/<\/mtable><\/mrow><mo>&InvisibleTimes;<\/mo><mrow><mtable>/g, ".");//ベクトル同士のかけ算になっている場合
		
		}
 }
 
//三角関数（累乗）

function Trigonometric(){
	ztri= z;
	TrigonometricSin();
	zsin= sinresult;
	
	TrigonometricCos();
	zcos= cosresult;
	
	TrigonometricTan();
	ztan= tanresult;
}

function TrigonometricSin(){
	var trisin= ztri.match(/<mi>sin<\/mi>/);
	if(trisin){
		trisl= RegExp.leftContext;
		trisr= "sin"+RegExp.rightContext;
		var trix1= trisr.match(/(sin<mo>\D<\/mo><\/mrow>\^<mn>(\d+)<\/mn><\/msup>)/);//sin^の形を抜き出す
		if(trix1){//三角関数が累乗の形になっていたら
			tri1= RegExp.$1;//sin^の部分だけ抜き出し
			tri12= RegExp.$2;//指数
			trix1r= "sin"+RegExp.rightContext;//sin(x)の形をつくる
			var trix1f= trix1r.match(/(sin.*?<\/mo><\/mrow>)/)
			if(trix1f){
				trix1ff= RegExp.$1;
				trix1fr= RegExp.rightContext;
			}
			sss= trisl+trix1ff+")^"+tri12;
			
			
			sinresult= sss+trix1fr;//sinが他にない場合
			
		}else{
			sinresult= ztri;//sinが累乗でない場合
		}
	}else{
			sinresult= ztri;//sinが存在しない場合
	}
}

function TrigonometricCos(){
	var tricos= zsin.match(/<mi>cos<\/mi>/);
	if(tricos){
		tricl= RegExp.leftContext;
		tricr= "cos"+RegExp.rightContext;
		var trix2= tricr.match(/(cos<mo>\D<\/mo><\/mrow>\^<mn>(\d+)<\/mn><\/msup>)/);//cos^の形を抜き出す
		if(trix2){//三角関数が累乗の形になっていたら
			tri2= RegExp.$1;//cos^の部分だけ抜き出し
			tri22= RegExp.$2;//指数
			trix2r= "cos"+RegExp.rightContext;//cos(x)の形を強引につくる
			var trix2f= trix2r.match(/(cos.*?<\/mo><\/mrow>)/)//そこからsin(x)を抜き出す
			if(trix2f){
				trix2ff= RegExp.$1;
				trix2fr= RegExp.rightContext;
			}
			ccc= tricl+trix2ff+")^"+tri22;
			
			cosresult= ccc+trix2fr;
			
		}else{
			cosresult= zsin;
		}
	}else{
		cosresult= zsin;
	}
}

function TrigonometricTan(){
	var tritan= zcos.match(/<mi>tan<\/mi>/);
	if(tritan){
		tritl= RegExp.leftContext;
		tritr= "tan"+RegExp.rightContext;
		var trix3= tritr.match(/(tan<mo>\D<\/mo><\/mrow>\^<mn>(\d+)<\/mn><\/msup>)/);//tan^の形を抜き出す
		if(trix3){//三角関数が累乗の形になっていたら
			tri3= RegExp.$1;//tan^の部分だけ抜き出し
			tri32= RegExp.$2;//指数
			trix3r= "tan"+RegExp.rightContext;//tan(x)の形を強引につくる
			
			var trix3f= trix3r.match(/(tan.*?<\/mo><\/mrow>)/)//そこからtan(x)を抜き出す
			if(trix3f){
				trix3ff= RegExp.$1;
				trix3fr= RegExp.rightContext;
			}
			
			ttt= tritl+trix3ff+")^"+tri32;
			
			tanresult= ttt+trix3fr;
			
		}else{
			tanresult= zcos;
		}
	}else{
		tanresult= zcos;//tanが存在しない場合
	}
}
