-- DROP FUNCTION public.proc_pos_dtempsalesline_insert(in varchar, in varchar, in varchar, in varchar, in varchar, in varchar, in varchar, in varchar, in varchar, in varchar, in varchar, in varchar, out json, out varchar);

CREATE OR REPLACE FUNCTION public.proc_pos_dtempsalesline_insert(p_ad_mclient_key character varying DEFAULT NULL::character varying, p_ad_morg_key character varying DEFAULT NULL::character varying, p_pos_mcashier_key character varying DEFAULT NULL::character varying, p_billno character varying DEFAULT NULL::character varying, p_sku character varying DEFAULT NULL::character varying, p_qty character varying DEFAULT NULL::character varying, p_price character varying DEFAULT NULL::character varying, p_discount character varying DEFAULT NULL::character varying, p_discountname character varying DEFAULT NULL::character varying, p_memberid character varying DEFAULT NULL::character varying, p_postby character varying DEFAULT NULL::character varying, p_ad_muser_key character varying DEFAULT NULL::character varying, OUT o_data json, OUT o_message character varying)
 RETURNS record
 LANGUAGE plpgsql
AS $function$
declare v_seqno integer;
		v_pos_mshop_key varchar(36);
	    v_total numeric;
	    v_strtotal varchar;
	    v_maxqty numeric;
	    v_qty numeric;
	   
	    v_totalamount numeric;
	    v_ispromo bool;
	    v_limitamount numeric;
	    v_ismurah bool;
	    v_pricediscount numeric;
	  	v_max_kelipatan_murah numeric;
		v_qty_promo_existing numeric;
	   	v_amountsalesexc numeric;   
	    v_sisa_kuota numeric;
	   
	    v_membername varchar;
	    v_memberid varchar;
	    v_membercardno varchar;
	    v_memberpoint numeric;
	    v_isbirthday bool;
	    v_qtysale numeric;
	    v_isbuyget bool;
		v_iscode bool;
	    v_qtybuy int;
		
	    v_discount_1 int;
		v_grosir bool;
		v_discount_name varchar;
	
	    v_qtyget int;
	    v_priceget int;
		v_max_qtyget int;
		v_totget int;
		
		v_skubuy varchar;

		rec RECORD;
		v_full_groups integer;
		v_discounted_units numeric;
		v_rem numeric;
		v_max_seqno integer;
		v_bundle_discount numeric;
		v_bundle_name varchar;
		v_totalbundlingqty numeric;
		v_bundle_min_qty int := 3; -- atau ambil dari db kalau mau fleksibel

		v_fk_discount numeric;
		v_fk_name varchar;
		v_fk_maxqty int;
		v_fk_discounted_qty int;
		v_fk_minbuy numeric;
		v_fk_total_qty numeric;


		v_fk_1 numeric;
		v_fk_2 numeric;
		v_fk_3 numeric;
		v_fk_4 numeric;
		 


BEGIN 

	v_qtyget = 0;
	v_priceget = 0;
	v_max_qtyget =0;
	v_totget =0;
	v_skubuy = 'aa';


	--kasih validasi jgn sampai p_qty decimal
--	IF p_qty <> TRUNC(p_qty) THEN
--	    o_message := 'Qty Tidak Boleh Desimal!';
--	    RETURN;
--	END IF;

	IF trim(p_qty) = '' OR NOT p_qty ~ '^[0-9]+$' THEN
        o_message := 'Qty harus berupa angka bulat positif.';
        RETURN;
    END IF;

   IF CAST(p_qty AS INTEGER) > 9999 THEN
	    o_message := 'Qty tidak boleh lebih dari 9999. Cek kembali apakah salah input';
	    RETURN;
	END IF;

	-- Buy & Get

--SELECT pos_dtempsalesline_key, ad_mclient_key, ad_morg_key, isactived, insertdate, insertby, postby, postdate, pos_mcashier_key, ad_muser_key, billno, seqno, sku, qty, price, discount, status, isrefund, memberid, ispromomurah, discountname, membername, isbirthday, memberpoint, membercardno, membertext, skubuy, reference_id
--FROM pos_dtempsalesline;
--SELECT pos_mproductdiscount_key, ad_mclient_key, ad_morg_key, isactived, insertdate, insertby, postby, postdate, discountname, discounttype, sku, discount, fromdate, todate, typepromo, maxqty, headername, jenis_promo
--FROM pos_mproductdiscount_bundling;
--SELECT pos_mproductdiscount_key, ad_mclient_key, ad_morg_key, bundling_code, minbuy, isactived, insertdate, insertby, postby, postdate
--FROM pos_mproductdiscount_bundling_header;

-- =========================================================
-- =============== PROMO FK (MAXQTY ONLY) ==================
-- =========================================================
v_fk_discount := NULL;
v_fk_name := NULL;
v_fk_maxqty := 0;
v_fk_discounted_qty := 0;
v_fk_minbuy := 0;

v_fk_1 := 0;
v_fk_2 := 0;
v_fk_3 := 0;
v_fk_4 := 0;


-- cek apakah SKU punya promo FK aktif
IF EXISTS (
    SELECT 1
    FROM pos_mproductdiscount_bundling b
    WHERE lower(b.discountname) LIKE 'fk%'
      AND lower(b.sku) = lower(p_sku)
      AND current_date BETWEEN b.fromdate AND b.todate
) THEN
-- ambil data promo FK dan insert isbirthday jadi true
v_isbirthday := true; 
END IF;


	
	if exists(select 1 from pos_dtempbuyget where billno like p_billno and skuget like p_sku) then 
		-- select price,qtyget into v_priceget,v_max_qtyget from pos_dtempbuyget where skuget = p_sku and billno = p_billno; 
		
		
		select max(b.priceget), sum(qty*qtyget) into v_priceget, v_max_qtyget from pos_dtempsalesline a inner join 
		(select priceget, skubuy, qtyget from pos_mproductbuyget where typepromo like 'Reguler' 
		and DATE(now()) between fromdate and todate
		group by priceget, skubuy, qtyget) b on a.sku = b.skubuy and a.sku in 
		(select skubuy from pos_dtempbuyget where skuget = p_sku 
		and billno = p_billno);
		
		
		select into v_totget coalesce(sum(qty),0) from pos_dtempsalesline where discountname='Buy & Get' and ad_muser_key = p_ad_muser_key and sku = p_sku;
		-- (select skubuy from pos_dtempbuyget where skuget = p_sku and billno = p_billno);
		
		

		-- select price into v_priceget from pos_dtempbuyget where skuget = p_sku and billno = p_billno; 
		if exists(select 1 from pos_dtempsalesline where sku = p_sku and billno = p_billno and price = v_priceget and discountname = 'Buy & Get') then 
			select qty into v_qtyget from pos_dtempsalesline where sku = p_sku and billno = p_billno and price = v_priceget and discountname = 'Buy & Get'; 
			--kenapa ambil dari salesline temp karna klo di void qtybuyget nya msh ada, jd qty grosir nya ttp ngurangin 1
		end if;
	end if;

	-- Member CashBack
	-- if (p_sku = '123456789') then 
		   -- o_message='Item ini Tidak Bisa di Void !';
	-- end if;
if not exists (select 1 from pos_dcashierbalance where ad_muser_key=p_ad_muser_key and date(startdate) = date(now()) and status='RUNNING') then 
	o_message='User Sudah Tutup Kasir !';
else
	
if cast(replace(p_price,',','') as numeric) <> 0 then	
	if p_qty='' then 
		p_qty='1';
	end if;
	v_qtybuy = 0;	
	
		
		
	  if exists (select 1 from pos_mproduct where isactived='0' and sku like p_sku) then 
		   o_message='Item ini Sedang di Inventory !';
	  else
	  
	  
	  
	  
	  

-- ================= END FK ==================

	  
	  
	  
	  
			select into v_seqno count(*) from pos_dtempsalesline where billno like p_billno;
			if v_seqno=0 then 
			  		v_seqno=1;
			  	else
			  	   v_seqno=v_seqno+1;
			 end if;
		

			if exists(select 1 from pos_mproduct where sku like p_sku and isnosale=true) then
			   p_price=0;
			end if;
		
			v_iscode=false;
			if (p_discountname <>'') then
			
	            if exists(select 1 from pos_mproductdiscountmember where discountname ilike p_discountname and  sku like p_sku and DATE(now()) between fromdate and todate) then
				-- CHECK PROMO CODE
					v_iscode=true;
					      
				end if;
			end if;
		
		
		
		    select maxqty into v_maxqty from pos_mproductdiscount where sku like p_sku and discountname like p_discountname;
			
    
				if exists(select 1 from pos_mmember where (memberid = p_memberid or membercardno = p_memberid or nohp = p_memberid) and p_memberid <>'' ) then  
					
				     select name,memberid,membercardno,point,
				     case when to_char(dateofbirth,'DDMMYY') like to_char(now(),'DDMMYY') then true else false end
				     into v_membername,v_memberid,v_membercardno,v_memberpoint,v_isbirthday
				     from pos_mmember where (memberid = p_memberid or membercardno = p_memberid or nohp = p_memberid) and p_memberid <>'';
				    

				     --if not exists (select 1 from pos_dtempbuyget where billno like p_billno and skubuy like p_sku) then
					    if exists(select 1 from pos_mproductbuyget where skubuy = p_sku and DATE(now()) between fromdate and todate) then 
					       select qtybuy into v_qtybuy from pos_mproductbuyget where skubuy = p_sku and DATE(now()) between fromdate and todate;
					       if exists(select 1 from pos_dtempsalesline where sku = p_sku and (qty + cast(p_qty as numeric)) >= v_qtybuy ) then				         
					               v_isbuyget=true;
						           INSERT INTO public.pos_dtempbuyget
									(ad_mclient_key, ad_morg_key, isactived, insertdate, insertby, postby, postdate, billno,skubuy, skuget,qtyget, price,discountname,status,discount)
									select ad_mclient_key, ad_morg_key, isactived, now(), p_postby, p_postby, now(), p_billno,p_sku, skuget,qtyget, priceget,discountname,'WAIT',discount  from pos_mproductbuyget 
								   where skubuy = p_sku and DATE(now()) between fromdate and todate;	
							else 
							    if ( cast(p_qty as numeric) >= v_qtybuy) then
							          v_isbuyget=true;
						           INSERT INTO public.pos_dtempbuyget
									(ad_mclient_key, ad_morg_key, isactived, insertdate, insertby, postby, postdate, billno,skubuy, skuget,qtyget, price,discountname,status,discount)
									select ad_mclient_key, ad_morg_key, isactived, now(), p_postby, p_postby, now(), p_billno,p_sku, skuget,qtyget, priceget,discountname,'WAIT',discount from pos_mproductbuyget 							
									where skubuy = p_sku and typepromo like 'Reguler' and DATE(now()) between fromdate and todate;
							
							      end if;
							     
					       end if;
					    end if;
				     --end if;
					 
				    
				 else 
				    

				     --if not exists (select 1 from pos_dtempbuyget where billno like p_billno and skubuy like p_sku) then
					   if exists(select 1 from pos_mproductbuyget where skubuy = p_sku and typepromo like 'Reguler' and DATE(now()) between fromdate and todate) then 
					       select qtybuy into v_qtybuy from pos_mproductbuyget where skubuy = p_sku and typepromo like 'Reguler' and DATE(now()) between fromdate and todate;
					      
					       if exists(select 1 from pos_dtempsalesline where billno=p_billno and sku = p_sku and (qty + cast(p_qty as numeric)) >= v_qtybuy) then
					           v_isbuyget=true;
						           INSERT INTO public.pos_dtempbuyget
									(ad_mclient_key, ad_morg_key, isactived, insertdate, insertby, postby, postdate, billno,skubuy, skuget,qtyget, price,discountname,status,discount)
									select ad_mclient_key, ad_morg_key, isactived, now(), p_postby, p_postby, now(), p_billno,p_sku, skuget,qtyget, priceget,discountname,'WAIT',discount from pos_mproductbuyget 							
									where skubuy = p_sku and typepromo like 'Reguler' and DATE(now()) between fromdate and todate;
							else 
							    if ( cast(p_qty as numeric) >= v_qtybuy) then
							          v_isbuyget=true;
						           INSERT INTO public.pos_dtempbuyget
									(ad_mclient_key, ad_morg_key, isactived, insertdate, insertby, postby, postdate, billno,skubuy, skuget,qtyget, price,discountname,status,discount)
									select ad_mclient_key, ad_morg_key, isactived, now(), p_postby, p_postby, now(), p_billno,p_sku, skuget,qtyget, priceget,discountname,'WAIT',discount from pos_mproductbuyget 							
									where skubuy = p_sku and typepromo like 'Reguler' and DATE(now()) between fromdate and todate;
							
							      end if;
								
					       end if;
					      
					    end if;
					  --end if;
				      
				end if;
			
		

				if exists(select 1 from ad_morg where isqty=true ) then			
				   select SUM(qty) into v_qtysale from pos_dtempsalesline where billno like p_billno and sku like p_sku ;
				   v_qtysale=coalesce(v_qtysale,0) + cast(p_qty as numeric);
	 
			   		if exists(select 1 from pos_mproduct where sku like p_sku and (stockqty - v_qtysale) < 0 and not left(p_sku,3)='839' and not sku='8320100000139') then
							o_message='Tidak Ada Stok atau Stok Kurang !!';
					else
		  
					    

							 if exists (select 1 from pos_mproductdiscountmurah where sku like p_sku and DATE(now()) between fromdate and todate) then 
							 -- Ambil data tebus murah
							    select pricediscount, limitamount, max_kelipatan 
							    into v_pricediscount, v_limitamount, v_max_kelipatan_murah
							    from pos_mproductdiscountmurah 
							    where sku like p_sku 
							    and date(now()) between fromdate and todate;
							    
							    -- Hitung total belanja exclude kategori dari KERANJANG AKTIF
							    select coalesce(sum((price - discount) * qty), 0)
							    into v_amountsalesexc
							    from pos_dtempsalesline
							    where billno like p_billno
							    and left(sku, 3) not in (
							        select trim(unnest(string_to_array(cat_exclude, ',')))
							        from pos_mproductdiscountmurah
							        where sku like p_sku
							        and date(now()) between fromdate and todate
							        and coalesce(cat_exclude, '') <> ''
							    );
							    
							    -- Hitung qty tebus murah yang SUDAH ada di keranjang
							    select coalesce(sum(qty), 0) into v_qty_promo_existing
							    from pos_dtempsalesline
							    where billno like p_billno
							    and sku like p_sku
							    and ispromomurah = true;
							    
							    -- Cek apakah memenuhi syarat tebus murah
							    if v_amountsalesexc >= v_limitamount 
							       and v_qty_promo_existing < coalesce(v_max_kelipatan_murah, 999999) then
							        v_ismurah := true;
							    end if;
							end if;
							
							v_discount_1=p_discount;
							v_discount_name=p_discountname;							
							-- Grosir
							if(p_discountname != 'Buy & Get') then
								if(v_iscode=false) then
									if exists (select 1 from pos_mproductdiscountgrosir_new where sku like p_sku and DATE(now()) between fromdate and todate) then
										select discount, discountname into v_discount_1, v_discount_name  from pos_mproductdiscountgrosir_new where sku like p_sku and DATE(now()) between fromdate and todate and 
										cast(v_qtysale as numeric) - v_qtyget >= minbuy order by minbuy desc limit 1;
									end if;
								end if;
							else 
								if(cast(replace(p_price,',','') as numeric) = 1) then
								
									v_discount_1=cast(replace(p_price,',','') as numeric);
									
								end if;
							end if;	

							if (v_ismurah = true) then
    begin
        v_sisa_kuota := coalesce(v_max_kelipatan_murah, 1) - v_qty_promo_existing;
        
        if cast(p_qty as numeric) <= v_sisa_kuota then
            -- Semua qty bisa kena tebus murah
            insert into pos_dtempsalesline(
                isactived, insertby, insertdate, postby, postdate,
                ad_mclient_key, ad_morg_key, pos_mcashier_key, ad_muser_key,
                billno, seqno, sku, qty, price, discount, discountname,
                memberid, membername, isbirthday, memberpoint, membercardno, membertext,
                status, ispromomurah
            )
            values(
                '1', p_postby, now(), p_postby, now(),
                p_ad_mclient_key, p_ad_morg_key, p_pos_mcashier_key, p_ad_muser_key,
                p_billno, v_seqno, p_sku,
                cast(p_qty as numeric),
                cast(replace(p_price, ',', '') as numeric),  -- harga normal
                v_pricediscount,                              -- diskon tebus murah
                'Tebus Murah 50k',                            -- atau ambil dari tabel
                v_memberid, v_membername, v_isbirthday, v_memberpoint, v_membercardno, p_memberid,
                'WAITING', true
            );
            o_message := 'Success';
            
        elsif v_sisa_kuota > 0 then
            -- Sebagian tebus murah, sebagian normal
            
            -- Insert yang tebus murah
            insert into pos_dtempsalesline(
                isactived, insertby, insertdate, postby, postdate,
                ad_mclient_key, ad_morg_key, pos_mcashier_key, ad_muser_key,
                billno, seqno, sku, qty, price, discount, discountname,
                memberid, membername, isbirthday, memberpoint, membercardno, membertext,
                status, ispromomurah
            )
            values(
                '1', p_postby, now(), p_postby, now(),
                p_ad_mclient_key, p_ad_morg_key, p_pos_mcashier_key, p_ad_muser_key,
                p_billno, v_seqno, p_sku,
                v_sisa_kuota,                                 -- qty tebus murah
                cast(replace(p_price, ',', '') as numeric),   -- harga normal
                v_pricediscount,                               -- diskon tebus murah
                'Tebus Murah 50k',
                v_memberid, v_membername, v_isbirthday, v_memberpoint, v_membercardno, p_memberid,
                'WAITING', true
            );
            
            -- Insert sisanya harga normal
            v_seqno := v_seqno + 1;
            insert into pos_dtempsalesline(
                isactived, insertby, insertdate, postby, postdate,
                ad_mclient_key, ad_morg_key, pos_mcashier_key, ad_muser_key,
                billno, seqno, sku, qty, price, discount, discountname,
                memberid, membername, isbirthday, memberpoint, membercardno, membertext,
                status, ispromomurah
            )
            values(
                '1', p_postby, now(), p_postby, now(),
                p_ad_mclient_key, p_ad_morg_key, p_pos_mcashier_key, p_ad_muser_key,
                p_billno, v_seqno, p_sku,
                cast(p_qty as numeric) - v_sisa_kuota,        -- sisa qty normal
                cast(replace(p_price, ',', '') as numeric),   -- harga normal
                0,                                             -- no discount
                '',                                            -- no discount name
                v_memberid, v_membername, v_isbirthday, v_memberpoint, v_membercardno, p_memberid,
                'WAITING', false
            );
            
            o_message := 'Success';
            
        else
            -- Kuota sudah habis, semua harga normal
            insert into pos_dtempsalesline(
                isactived, insertby, insertdate, postby, postdate,
                ad_mclient_key, ad_morg_key, pos_mcashier_key, ad_muser_key,
                billno, seqno, sku, qty, price, discount, discountname,
                memberid, membername, isbirthday, memberpoint, membercardno, membertext,
                status, ispromomurah
            )
            values(
                '1', p_postby, now(), p_postby, now(),
                p_ad_mclient_key, p_ad_morg_key, p_pos_mcashier_key, p_ad_muser_key,
                p_billno, v_seqno, p_sku,
                cast(p_qty as numeric),
                cast(replace(p_price, ',', '') as numeric),
                0,
                '',
                v_memberid, v_membername, v_isbirthday, v_memberpoint, v_membercardno, p_memberid,
                'WAITING', false
            );
            o_message := 'Success';
        end if;
    end;			    			
							else		    					
							
									-- and price-v_discount_1=cast(replace(p_price,',','') as numeric)
								    if not exists (select 1 from pos_dtempsalesline where billno like p_billno and sku like p_sku and price=cast(replace(p_price,',','') as numeric) and discount = v_discount_1 and (discountname != 'Buy & Get' or discountname is null)) then
									    
										
									       if(v_discount_name='Buy & Get') then
												if(cast(p_qty as numeric) > v_max_qtyget) THEN
													o_message='QTY GET TIDAK BISA DIUBAH';
												ELSE
												
												
													if not exists (select 1 from pos_dtempsalesline where billno like p_billno and sku like p_sku and discountname = 'Buy & Get') then
													
														insert into pos_dtempsalesline(isactived,insertby,insertdate,postby,postdate,ad_mclient_key,ad_morg_key,pos_mcashier_key,ad_muser_key,billno,seqno,sku,qty,price,discount,discountname ,
														memberid,membername,isbirthday,memberpoint,membercardno,membertext,status)
														values('1',p_postby,now(),p_postby,now(),p_ad_mclient_key,p_ad_morg_key,p_pos_mcashier_key,p_ad_muser_key,p_billno,v_seqno,p_sku,cast(p_qty as numeric),
														cast(replace(p_price,',','') as numeric),COALESCE(cast(v_discount_1 as numeric), 0),v_discount_name,v_memberid,v_membername,v_isbirthday,v_memberpoint,v_membercardno,p_memberid ,'WAITING');
													else 
														update pos_dtempsalesline set qty=qty+cast(p_qty as numeric) where billno like p_billno and sku like p_sku and discountname = 'Buy & Get';	
												
													end if;
												
															if exists (select 1 from pos_dtempbuyget where billno like p_billno and status like 'WAIT') then 
																update pos_dtempbuyget set
																status ='CLOSE'
																where billno like p_billno and status like 'WAIT';
															end if;
													
													o_message='Success';
												end if;
											else 
													insert into pos_dtempsalesline(isactived,insertby,insertdate,postby,postdate,ad_mclient_key,ad_morg_key,pos_mcashier_key,ad_muser_key,billno,seqno,sku,qty,price,discount,discountname ,memberid,membername,isbirthday,memberpoint,membercardno,membertext,status)
													values('1',p_postby,now(),p_postby,now(),p_ad_mclient_key,p_ad_morg_key,p_pos_mcashier_key,p_ad_muser_key,p_billno,v_seqno,p_sku,cast(p_qty as numeric),
														   cast(replace(p_price,',','') as numeric),COALESCE(cast(v_discount_1 as numeric), 0),v_discount_name,v_memberid,v_membername,v_isbirthday,v_memberpoint,v_membercardno,p_memberid ,'WAITING');
																		 	
													o_message='Success';
											end if;
												
									else 
									
										if(v_discount_name='Buy & Get') then
											
												if(cast(p_qty as numeric) + v_totget > v_max_qtyget) THEN
													
													-- o_message='Qty Hadiah tidak bisa melebihi Qty Beli !!';
													
													if not exists (select 1 from pos_dtempsalesline where billno like p_billno and sku like p_sku and discountname != 'Buy & Get') then
													
														insert into pos_dtempsalesline(isactived,insertby,insertdate,postby,postdate,ad_mclient_key,ad_morg_key,pos_mcashier_key,ad_muser_key,billno,seqno,sku,qty,price,discount,discountname ,memberid,membername,isbirthday,memberpoint,membercardno,membertext,status)
														values('1',p_postby,now(),p_postby,now(),p_ad_mclient_key,p_ad_morg_key,p_pos_mcashier_key,p_ad_muser_key,p_billno,v_seqno,p_sku,cast(p_qty as numeric),
														cast(replace(p_price,',','') as numeric),COALESCE(cast(v_discount_1 as numeric), 0),v_discount_name,v_memberid,v_membername,v_isbirthday,v_memberpoint,v_membercardno,p_memberid ,'WAITING');
														o_message='Success';
														
													else 
														update pos_dtempsalesline set qty=qty+cast(p_qty as numeric) where billno like p_billno and sku like p_sku and discountname != 'Buy & Get';	
														-- o_message=cast(p_qty as numeric);
														o_message='Success';
												
													end if;
													
													
												ELSE
													if not exists (select 1 from pos_dtempsalesline where billno like p_billno and sku like p_sku and discountname = 'Buy & Get') then
													
														insert into pos_dtempsalesline(isactived,insertby,insertdate,postby,postdate,ad_mclient_key,ad_morg_key,pos_mcashier_key,ad_muser_key,billno,seqno,sku,qty,price,discount,discountname ,memberid,membername,isbirthday,memberpoint,membercardno,membertext,status)
														values('1',p_postby,now(),p_postby,now(),p_ad_mclient_key,p_ad_morg_key,p_pos_mcashier_key,p_ad_muser_key,p_billno,v_seqno,p_sku,cast(p_qty as numeric),
														cast(replace(p_price,',','') as numeric),COALESCE(cast(v_discount_1 as numeric), 0),v_discount_name,v_memberid,v_membername,v_isbirthday,v_memberpoint,v_membercardno,p_memberid ,'WAITING');
														o_message='Success';
														
													else 
														update pos_dtempsalesline set qty=qty+cast(p_qty as numeric) where billno like p_billno and sku like p_sku and discountname = 'Buy & Get';	
														-- o_message=cast(p_qty as numeric);
														o_message='Success';
												
													end if;
												end if;
			
											else
											update pos_dtempsalesline set
														    qty=qty+cast(p_qty as numeric),						        
														    memberid=v_memberid,
															membername =v_membername,
															membercardno=v_membercardno,
															memberpoint =v_memberpoint,
															isbirthday=v_isbirthday,
															membertext=p_memberid,
															discount=COALESCE(cast(v_discount_1 as numeric), 0),
															discountname=v_discount_name
											where billno like p_billno and sku like p_sku and price=cast(replace(p_price,',','') as numeric) and discount = v_discount_1 and (discountname != 'Buy & Get' or discountname is null);	
										
											o_message='Success';
										 
										end if; 
										 
									end if;
								
							end if;
						
						
						
		
						end if;	
				else
				
					select SUM(qty) into v_qtysale from pos_dtempsalesline where billno like p_billno and sku like p_sku ;
				    v_qtysale=coalesce(v_qtysale,0) + cast(p_qty as numeric);

							 if exists (select 1 from pos_mproductdiscountmurah where sku like p_sku and date(now()) between fromdate and todate) then
    -- Ambil data tebus murah
							    select pricediscount, limitamount, max_kelipatan 
							    into v_pricediscount, v_limitamount, v_max_kelipatan_murah
							    from pos_mproductdiscountmurah 
							    where sku like p_sku 
							    and date(now()) between fromdate and todate;
							    
							    -- Hitung total belanja exclude kategori dari KERANJANG AKTIF
							    select coalesce(sum((price - discount) * qty), 0)
							    into v_amountsalesexc
							    from pos_dtempsalesline
							    where billno like p_billno
							    and left(sku, 3) not in (
							        select trim(unnest(string_to_array(cat_exclude, ',')))
							        from pos_mproductdiscountmurah
							        where sku like p_sku
							        and date(now()) between fromdate and todate
							        and coalesce(cat_exclude, '') <> ''
							    );
							    
							    -- Hitung qty tebus murah yang SUDAH ada di keranjang
							    select coalesce(sum(qty), 0) into v_qty_promo_existing
							    from pos_dtempsalesline
							    where billno like p_billno
							    and sku like p_sku
							    and ispromomurah = true;
							    
							    -- Cek apakah memenuhi syarat tebus murah
							    if v_amountsalesexc >= v_limitamount 
							       and v_qty_promo_existing < coalesce(v_max_kelipatan_murah, 999999) then
							        v_ismurah := true;
							    end if;
							end if;

							-- Grosir
							v_discount_1=p_discount;
							v_discount_name=p_discountname;	
							if(p_discountname != 'Buy & Get') then
								if(v_iscode=false) then
									if exists (select 1 from pos_mproductdiscountgrosir_new where sku like p_sku and DATE(now()) between fromdate and todate) then
										select discount, discountname into v_discount_1, v_discount_name  from pos_mproductdiscountgrosir_new where sku like p_sku and DATE(now()) between fromdate and todate and 
										cast(v_qtysale as numeric) - v_qtyget >= minbuy order by minbuy desc limit 1;
									end if;
								end if;
								
							else 
								if(cast(replace(p_price,',','') as numeric) = 1) then
								
									v_discount_1=cast(replace(p_price,',','') as numeric);
									
								end if;
							end if;	


if (v_ismurah = true) then
        
    begin
        v_sisa_kuota := coalesce(v_max_kelipatan_murah, 1) - v_qty_promo_existing;
        
        if cast(p_qty as numeric) <= v_sisa_kuota then
            -- Semua qty bisa kena tebus murah
            insert into pos_dtempsalesline(
                isactived, insertby, insertdate, postby, postdate,
                ad_mclient_key, ad_morg_key, pos_mcashier_key, ad_muser_key,
                billno, seqno, sku, qty, price, discount, discountname,
                memberid, membername, isbirthday, memberpoint, membercardno, membertext,
                status, ispromomurah
            )
            values(
                '1', p_postby, now(), p_postby, now(),
                p_ad_mclient_key, p_ad_morg_key, p_pos_mcashier_key, p_ad_muser_key,
                p_billno, v_seqno, p_sku,
                cast(p_qty as numeric),
                cast(replace(p_price, ',', '') as numeric),  -- harga normal
                v_pricediscount,                              -- diskon tebus murah
                'Tebus Murah 50k',                            -- atau ambil dari tabel
                v_memberid, v_membername, v_isbirthday, v_memberpoint, v_membercardno, p_memberid,
                'WAITING', true
            );
            o_message := 'Success';
            
        elsif v_sisa_kuota > 0 then
            -- Sebagian tebus murah, sebagian normal
            
            -- Insert yang tebus murah
            insert into pos_dtempsalesline(
                isactived, insertby, insertdate, postby, postdate,
                ad_mclient_key, ad_morg_key, pos_mcashier_key, ad_muser_key,
                billno, seqno, sku, qty, price, discount, discountname,
                memberid, membername, isbirthday, memberpoint, membercardno, membertext,
                status, ispromomurah
            )
            values(
                '1', p_postby, now(), p_postby, now(),
                p_ad_mclient_key, p_ad_morg_key, p_pos_mcashier_key, p_ad_muser_key,
                p_billno, v_seqno, p_sku,
                v_sisa_kuota,                                 -- qty tebus murah
                cast(replace(p_price, ',', '') as numeric),   -- harga normal
                v_pricediscount,                               -- diskon tebus murah
                'Tebus Murah 50k',
                v_memberid, v_membername, v_isbirthday, v_memberpoint, v_membercardno, p_memberid,
                'WAITING', true
            );
            
            -- Insert sisanya harga normal
            v_seqno := v_seqno + 1;
            insert into pos_dtempsalesline(
                isactived, insertby, insertdate, postby, postdate,
                ad_mclient_key, ad_morg_key, pos_mcashier_key, ad_muser_key,
                billno, seqno, sku, qty, price, discount, discountname,
                memberid, membername, isbirthday, memberpoint, membercardno, membertext,
                status, ispromomurah
            )
            values(
                '1', p_postby, now(), p_postby, now(),
                p_ad_mclient_key, p_ad_morg_key, p_pos_mcashier_key, p_ad_muser_key,
                p_billno, v_seqno, p_sku,
                cast(p_qty as numeric) - v_sisa_kuota,        -- sisa qty normal
                cast(replace(p_price, ',', '') as numeric),   -- harga normal
                0,                                             -- no discount
                '',                                            -- no discount name
                v_memberid, v_membername, v_isbirthday, v_memberpoint, v_membercardno, p_memberid,
                'WAITING', false
            );
            
            o_message := 'Success';
            
        else
            -- Kuota sudah habis, semua harga normal
            insert into pos_dtempsalesline(
                isactived, insertby, insertdate, postby, postdate,
                ad_mclient_key, ad_morg_key, pos_mcashier_key, ad_muser_key,
                billno, seqno, sku, qty, price, discount, discountname,
                memberid, membername, isbirthday, memberpoint, membercardno, membertext,
                status, ispromomurah
            )
            values(
                '1', p_postby, now(), p_postby, now(),
                p_ad_mclient_key, p_ad_morg_key, p_pos_mcashier_key, p_ad_muser_key,
                p_billno, v_seqno, p_sku,
                cast(p_qty as numeric),
                cast(replace(p_price, ',', '') as numeric),
                0,
                '',
                v_memberid, v_membername, v_isbirthday, v_memberpoint, v_membercardno, p_memberid,
                'WAITING', false
            );
            o_message := 'Success';
        end if;
    end;		    			
								else		    					
									    if not exists (select 1 from pos_dtempsalesline where billno like p_billno and sku like p_sku and price=cast(replace(p_price,',','') as numeric) and discount = v_discount_1 and (discountname != 'Buy & Get' or discountname is null)) then
											
											if(v_discount_name='Buy & Get') then
											
												if(cast(p_qty as numeric) > v_max_qtyget) THEN
													o_message='QTY GET TIDAK BISA DIUBAH';
												ELSE
													if not exists (select 1 from pos_dtempsalesline where billno like p_billno and sku like p_sku and discountname = 'Buy & Get') then
													
														insert into pos_dtempsalesline(isactived,insertby,insertdate,postby,postdate,ad_mclient_key,ad_morg_key,pos_mcashier_key,ad_muser_key,billno,seqno,sku,qty,price,discount,discountname ,
														memberid,membername,isbirthday,memberpoint,membercardno,membertext,status)
														values('1',p_postby,now(),p_postby,now(),p_ad_mclient_key,p_ad_morg_key,p_pos_mcashier_key,p_ad_muser_key,p_billno,v_seqno,p_sku,cast(p_qty as numeric),
														cast(replace(p_price,',','') as numeric),COALESCE(cast(v_discount_1 as numeric), 0),v_discount_name,v_memberid,v_membername,v_isbirthday,v_memberpoint,v_membercardno,p_memberid ,'WAITING');
													else 
														update pos_dtempsalesline set qty=qty+cast(p_qty as numeric) where billno like p_billno and sku like p_sku and discountname = 'Buy & Get';	
												
													end if;
												
															if exists (select 1 from pos_dtempbuyget where billno like p_billno and status like 'WAIT') then 
																update pos_dtempbuyget set
																status ='CLOSE'
																where billno like p_billno and status like 'WAIT';
															end if;
													
													o_message='Success';
												end if;
											else 
													insert into pos_dtempsalesline(isactived,insertby,insertdate,postby,postdate,ad_mclient_key,ad_morg_key,pos_mcashier_key,ad_muser_key,billno,seqno,sku,qty,price,discount,discountname ,memberid,membername,isbirthday,memberpoint,membercardno,membertext,status)
													values('1',p_postby,now(),p_postby,now(),p_ad_mclient_key,p_ad_morg_key,p_pos_mcashier_key,p_ad_muser_key,p_billno,v_seqno,p_sku,cast(p_qty as numeric),
														   cast(replace(p_price,',','') as numeric),COALESCE(cast(v_discount_1 as numeric), 0),v_discount_name,v_memberid,v_membername,v_isbirthday,v_memberpoint,v_membercardno,p_memberid ,'WAITING');
																		 	
													o_message='Success';
											end if;
										
									       
												
										else 
										
											if(v_discount_name='Buy & Get') then
											
												if(cast(p_qty as numeric) + v_totget > v_max_qtyget) THEN
													
													-- o_message='Qty Hadiah tidak bisa melebihi Qty Beli !!';
													
													if not exists (select 1 from pos_dtempsalesline where billno like p_billno and sku like p_sku and discountname != 'Buy & Get') then
													
														insert into pos_dtempsalesline(isactived,insertby,insertdate,postby,postdate,ad_mclient_key,ad_morg_key,pos_mcashier_key,ad_muser_key,billno,seqno,sku,qty,price,discount,discountname ,memberid,membername,isbirthday,memberpoint,membercardno,membertext,status)
														values('1',p_postby,now(),p_postby,now(),p_ad_mclient_key,p_ad_morg_key,p_pos_mcashier_key,p_ad_muser_key,p_billno,v_seqno,p_sku,cast(p_qty as numeric),
														cast(replace(p_price,',','') as numeric),COALESCE(cast(v_discount_1 as numeric), 0),v_discount_name,v_memberid,v_membername,v_isbirthday,v_memberpoint,v_membercardno,p_memberid ,'WAITING');
														o_message='Success';
														
													else 
														update pos_dtempsalesline set qty=qty+cast(p_qty as numeric) where billno like p_billno and sku like p_sku and discountname != 'Buy & Get';	
														-- o_message=cast(p_qty as numeric);
														o_message='Success';
												
													end if;
													
													
												ELSE
													if not exists (select 1 from pos_dtempsalesline where billno like p_billno and sku like p_sku and discountname = 'Buy & Get') then
													
														insert into pos_dtempsalesline(isactived,insertby,insertdate,postby,postdate,ad_mclient_key,ad_morg_key,pos_mcashier_key,ad_muser_key,billno,seqno,sku,qty,price,discount,discountname ,memberid,membername,isbirthday,memberpoint,membercardno,membertext,status)
														values('1',p_postby,now(),p_postby,now(),p_ad_mclient_key,p_ad_morg_key,p_pos_mcashier_key,p_ad_muser_key,p_billno,v_seqno,p_sku,cast(p_qty as numeric),
														cast(replace(p_price,',','') as numeric),COALESCE(cast(v_discount_1 as numeric), 0),v_discount_name,v_memberid,v_membername,v_isbirthday,v_memberpoint,v_membercardno,p_memberid ,'WAITING');
														o_message='Success';
														
													else 
														update pos_dtempsalesline set qty=qty+cast(p_qty as numeric) where billno like p_billno and sku like p_sku and discountname = 'Buy & Get';	
														-- o_message=cast(p_qty as numeric);
														o_message='Success';
												
													end if;
												end if;
			
											else
												update pos_dtempsalesline set
															    qty=qty+cast(p_qty as numeric),						        
															    memberid=v_memberid,
																membername =v_membername,
																membercardno=v_membercardno,
																memberpoint =v_memberpoint,
																isbirthday=v_isbirthday,
																membertext=p_memberid,
																discount=COALESCE(cast(v_discount_1 as numeric), 0),
																discountname=v_discount_name
												where billno like p_billno and sku like p_sku and price=cast(replace(p_price,',','') as numeric) and discount = v_discount_1 and (discountname != 'Buy & Get' or discountname is null);	
											
												o_message='Success';
											 
											end if; 
											 
										end if;
																	 		
								end if;
								

					end if;
				end if;



			v_bundle_discount := NULL;
			v_bundle_name := NULL;
			v_totalbundlingqty := 0;

			-- cek apakah sku ini bagian dari group bundling
			IF EXISTS (
				SELECT 1
				FROM pos_mproductdiscount_bundling b
				WHERE lower(b.discountname) LIKE 'pk%'
				AND b.sku = p_sku
				AND current_date BETWEEN b.fromdate AND b.todate
			) THEN
				-- ambil group dan diskon terbesar
				SELECT b.discount, b.discountname
				INTO v_bundle_discount, v_bundle_name
				FROM pos_mproductdiscount_bundling b
				WHERE lower(b.discountname) LIKE 'pk%'
				AND b.sku = p_sku
				AND current_date BETWEEN b.fromdate AND b.todate
				ORDER BY b.discount DESC
				LIMIT 1;
			
				IF v_bundle_name IS NOT NULL THEN
					SELECT h.minbuy
					INTO v_bundle_min_qty
					FROM pos_mproductdiscount_bundling_header h
					WHERE lower(h.bundling_code) = lower(v_bundle_name)
					AND h.isactived = 'Y'
					ORDER BY h.insertdate DESC
					LIMIT 1;

					IF v_bundle_min_qty IS NULL THEN
						v_bundle_min_qty := 1; -- fallback biar ga error division by zero
					END IF;
				END IF;

				-- hitung ulang total qty group dari tabel yg SUDAH update (semua sku dalam group)
				SELECT COALESCE(SUM(d.qty),0)
				INTO v_totalbundlingqty
				FROM pos_dtempsalesline d
				JOIN pos_mproductdiscount_bundling b2 ON lower(d.sku)=lower(b2.sku)
				WHERE d.billno = p_billno
				AND lower(b2.discountname)=lower(v_bundle_name)
				AND current_date BETWEEN b2.fromdate AND b2.todate;

				-- hitung total unit yg berhak diskon
				v_full_groups := floor(v_totalbundlingqty / v_bundle_min_qty);
				v_discounted_units := v_full_groups * v_bundle_min_qty;

				-- hanya kalau sudah memenuhi syarat bundling kita reset + alokasikan
				IF v_discounted_units > 0 THEN

					-- reset semua diskon untuk sku dalam group bundling (biar bisa reassign bundling)
					UPDATE pos_dtempsalesline d
					SET discount = 0, discountname = ''
					FROM pos_mproductdiscount_bundling b
					WHERE lower(b.discountname) = lower(v_bundle_name)
					AND lower(d.sku) = lower(b.sku)
					AND d.billno = p_billno
					AND current_date BETWEEN b.fromdate AND b.todate;

					-- alokasi bundling FIFO
					v_rem := v_discounted_units;
					SELECT COALESCE(MAX(seqno),0)
					INTO v_max_seqno FROM pos_dtempsalesline WHERE billno = p_billno;

					FOR rec IN
						SELECT d.pos_dtempsalesline_key, d.seqno, d.sku, d.qty, d.price,
							d.isactived, d.insertby, d.postby,
							d.ad_mclient_key, d.ad_morg_key, d.pos_mcashier_key, d.ad_muser_key,
							d.billno, d.memberid, d.membername, d.isbirthday,
							d.memberpoint, d.membercardno, d.membertext, d.status,
							b2.discount AS b_discount, b2.discountname AS b_name
						FROM pos_dtempsalesline d
						JOIN pos_mproductdiscount_bundling b2 ON lower(d.sku)=lower(b2.sku)
						WHERE d.billno = p_billno
						AND lower(b2.discountname)=lower(v_bundle_name)
						AND current_date BETWEEN b2.fromdate AND b2.todate
						ORDER BY d.seqno
					LOOP
						EXIT WHEN v_rem <= 0;

						IF rec.qty <= v_rem THEN
							UPDATE pos_dtempsalesline
							SET discount = rec.b_discount,
								discountname = rec.b_name
							WHERE pos_dtempsalesline_key = rec.pos_dtempsalesline_key;

							v_rem := v_rem - rec.qty;
						ELSE
							-- split baris: sisakan qty tanpa diskon, insert baris bundling sebanyak v_rem
							UPDATE pos_dtempsalesline
							SET qty = qty - v_rem
							WHERE pos_dtempsalesline_key = rec.pos_dtempsalesline_key;

							v_max_seqno := v_max_seqno + 1;

							INSERT INTO pos_dtempsalesline (
								pos_dtempsalesline_key, isactived, insertdate, insertby, postby, postdate,
								ad_mclient_key, ad_morg_key, pos_mcashier_key, ad_muser_key, billno, seqno,
								sku, qty, price, discount, discountname,
								memberid, membername, isbirthday, memberpoint, membercardno, membertext, status
							)
							VALUES (
								get_uuid(), rec.isactived, now(), rec.insertby, rec.postby, now(),
								rec.ad_mclient_key, rec.ad_morg_key, rec.pos_mcashier_key, rec.ad_muser_key,
								rec.billno, v_max_seqno,
								rec.sku, v_rem, rec.price, rec.b_discount, rec.b_name,
								rec.memberid, rec.membername, rec.isbirthday,
								rec.memberpoint, rec.membercardno, rec.membertext, rec.status
							);

							v_rem := 0;
						END IF;
					END LOOP;

					-- === RE-APPLY REGULER/GROSIR ke sisa unit (unit yg TIDAK dapat bundling)
					FOR rec IN
						SELECT DISTINCT b.sku
						FROM pos_mproductdiscount_bundling b
						WHERE lower(b.discountname)=lower(v_bundle_name)
						AND current_date BETWEEN b.fromdate AND b.todate
					LOOP
						-- total qty untuk sku ini (setelah alokasi bundling di atas)
						SELECT COALESCE(SUM(qty),0) INTO v_qtysale
						FROM pos_dtempsalesline
						WHERE billno = p_billno
						AND lower(sku) = lower(rec.sku);

						-- apply grosir/reguler
						v_discount_1 := NULL;
						v_discount_name := NULL;

						IF EXISTS (
							SELECT 1 FROM pos_mproductdiscountgrosir_new g
							WHERE lower(g.sku)=lower(rec.sku)
							AND current_date BETWEEN g.fromdate AND g.todate
							AND v_qtysale >= g.minbuy
						) THEN
							SELECT discount, discountname
							INTO v_discount_1, v_discount_name
							FROM pos_mproductdiscountgrosir_new g
							WHERE lower(g.sku)=lower(rec.sku)
							AND current_date BETWEEN g.fromdate AND g.todate
							AND v_qtysale >= g.minbuy
							ORDER BY g.minbuy DESC
							LIMIT 1;
						ELSE
							-- fallback: cek pos_mproductdiscount (diskon reguler / promo single-item)
							IF EXISTS (
								SELECT 1 FROM pos_mproductdiscount m
								WHERE lower(m.sku)=lower(rec.sku)
								AND current_date BETWEEN m.fromdate AND m.todate
							) THEN
								SELECT COALESCE(discount,0), COALESCE(discountname,'')
								INTO v_discount_1, v_discount_name
								FROM pos_mproductdiscount m
								WHERE lower(m.sku)=lower(rec.sku)
								AND current_date BETWEEN m.fromdate AND m.todate
								ORDER BY m.insertdate DESC
								LIMIT 1;
							END IF;
						END IF;

						-- apply re-calculated reguler discount ke semua baris sisa (yang tidak bundling)
						IF COALESCE(v_discount_1,0) <> 0 OR COALESCE(v_discount_name,'') <> '' THEN
							UPDATE pos_dtempsalesline
							SET discount = COALESCE(v_discount_1,0),
								discountname = COALESCE(v_discount_name,'')
							WHERE billno = p_billno
							AND lower(sku) = lower(rec.sku)
							AND (COALESCE(discount,0) = 0 AND COALESCE(discountname,'') = '');
						END IF;
					END LOOP;
					-- === END RE-APPLY
				END IF; -- end if v_discounted_units > 0
			END IF; -- end if sku group exists

			-- === CONSOLIDATE / MERGE BARIS ===
			FOR rec IN
				SELECT sku, price, discount, discountname,
					array_agg(pos_dtempsalesline_key ORDER BY seqno) AS keys,
					SUM(qty) AS total_qty
				FROM pos_dtempsalesline
				WHERE billno = p_billno
				GROUP BY sku, price, discount, discountname
				HAVING COUNT(*) > 1
			LOOP
				-- update baris pertama jadi total qty
				UPDATE pos_dtempsalesline
				SET qty = rec.total_qty
				WHERE pos_dtempsalesline_key = rec.keys[1];

				-- hapus baris sisanya
				DELETE FROM pos_dtempsalesline
				WHERE pos_dtempsalesline_key = ANY(rec.keys[2:array_length(rec.keys,1)]);
			END LOOP;
-- === END CONSOLIDATION




IF EXISTS (
    SELECT 1
    FROM pos_mproductdiscount_bundling pmb
    WHERE pmb.sku IN (
        SELECT pd.sku
        FROM pos_dtempsalesline pd
        WHERE pd.isbirthday = true
    )
    AND pmb.discountname NOT IN (
        SELECT pmb2.discountname
        FROM pos_mproductdiscount_bundling pmb2
        WHERE pmb2.sku NOT IN (
            SELECT pd2.sku
            FROM pos_dtempsalesline pd2
            WHERE pd2.isbirthday = true
        )
        GROUP BY pmb2.discountname
    )
) THEN

    -- ambil 1 discountname FK yang valid
    SELECT DISTINCT pmb.discountname
    INTO v_fk_name
    FROM pos_mproductdiscount_bundling pmb
    WHERE pmb.sku IN (
        SELECT pd.sku
        FROM pos_dtempsalesline pd
        WHERE pd.isbirthday = true
    )
    AND pmb.discountname NOT IN (
        SELECT pmb2.discountname
        FROM pos_mproductdiscount_bundling pmb2
        WHERE pmb2.sku NOT IN (
            SELECT pd2.sku
            FROM pos_dtempsalesline pd2
            WHERE pd2.isbirthday = true
        )
        GROUP BY pmb2.discountname
    )
    ORDER BY pmb.discountname
    LIMIT 1;

    -- update per PK + diskon per SKU
    FOR rec IN
        SELECT 
            pd.pos_dtempsalesline_key,
            pmb.discount
        FROM pos_dtempsalesline pd
        JOIN pos_mproductdiscount_bundling pmb
             ON pmb.sku = pd.sku
            AND pmb.discountname = v_fk_name
        WHERE pd.isbirthday = true
    LOOP
        UPDATE pos_dtempsalesline
        SET discount     = rec.discount,
            discountname = v_fk_name,
            isbirthday   = false
        WHERE pos_dtempsalesline_key = rec.pos_dtempsalesline_key;
    END LOOP;

END IF;
 	  	
 	  	-- Member CashBack
 	  	-- if exists(select 1 from pos_mmember where (memberid = p_memberid or membercardno = p_memberid or nohp = p_memberid) and p_memberid <>'' ) then  
					 -- select CAST(sum(((price-discount)*qty)*2.5/100) AS INT) into v_memberpoint from pos_dtempsalesline where billno like p_billno and discountname !='Cashback';
					
					 
					 -- if exists(select 1 from pos_dtempsalesline where billno like p_billno and discountname='Cashback') then  
					 
						-- v_memberpoint=0;
						-- if exists(select 1 from pos_dtempsalesline where billno like p_billno and discountname !='Cashback') then  
							-- select CAST(sum(((price-discount)*qty)*2.5/100) AS INT) into v_memberpoint from pos_dtempsalesline where billno like p_billno and discountname !='Cashback';
						-- end if;
					 
						-- update pos_dtempsalesline set price =v_memberpoint where billno like p_billno and discountname='Cashback';
					 -- else 
						-- insert into pos_dtempsalesline(isactived,insertby,insertdate,postby,postdate,ad_mclient_key,ad_morg_key,pos_mcashier_key,ad_muser_key,billno,seqno,sku,qty,price,discount,discountname ,memberid,membername,isbirthday,memberpoint,		membercardno,membertext,status)
						-- values('1',p_postby,now(),p_postby,now(),p_ad_mclient_key,p_ad_morg_key,p_pos_mcashier_key,p_ad_muser_key,p_billno,99,'123456789','-1',
						-- v_memberpoint,0,'Cashback',v_memberid,v_membername,v_isbirthday,0,v_membercardno,p_memberid ,'WAITING');
					 -- end if; 
		-- end if;
 
	 else
	 	o_message='Maaf Item ini belum bisa dijual ...';
	
	 end if;
end if;


-- RECALCULATE TEBUS MURAH
FOR rec IN
(
    SELECT
        d.pos_dtempsalesline_key,
        d.isactived,
        d.insertby,
        d.postby,
        d.ad_mclient_key,
        d.ad_morg_key,
        d.pos_mcashier_key,
        d.ad_muser_key,
        d.billno,
        d.seqno,
        d.sku,
        d.qty,
        d.price,
        d.status,
        d.memberid,
        d.membername,
        d.isbirthday,
        d.memberpoint,
        d.membercardno,
        d.membertext,

        pm.pricediscount,
        pm.limitamount,
        pm.discountname,
        COALESCE(pm.max_kelipatan,1) AS max_kelipatan

    FROM pos_dtempsalesline d
    INNER JOIN pos_mproductdiscountmurah pm
        ON pm.sku = d.sku
    WHERE d.billno = p_billno
      AND current_date BETWEEN pm.fromdate AND pm.todate
      AND COALESCE(d.ispromomurah,false)=false
)
LOOP

    ------------------------------------------------------------------
    -- HITUNG SALES EXCLUDE CATEGORY
    ------------------------------------------------------------------
    SELECT COALESCE(
        SUM((price-discount) * qty),
        0
    )
    INTO v_amountsalesexc
    FROM pos_dtempsalesline
    WHERE billno = p_billno
      AND LEFT(sku,3) NOT IN
      (
          SELECT TRIM(
                 UNNEST(
                 STRING_TO_ARRAY(
                 COALESCE(cat_exclude,''),
                 ','
                 )
                 )
                 )
          FROM pos_mproductdiscountmurah
          WHERE sku = rec.sku
      );

    ------------------------------------------------------------------
    -- SUDAH MEMENUHI SYARAT BELANJA
    ------------------------------------------------------------------
    IF v_amountsalesexc >= rec.limitamount THEN

        ------------------------------------------------------------------
        -- QTY LEBIH BESAR DARI MAX KELIPATAN
        ------------------------------------------------------------------
        IF rec.qty > rec.max_kelipatan THEN

            ------------------------------------------------------------------
            -- INSERT BARIS PROMO
            ------------------------------------------------------------------
            INSERT INTO pos_dtempsalesline
            (
                pos_dtempsalesline_key,
                isactived,
                insertdate,
                insertby,
                postby,
                postdate,
                ad_mclient_key,
                ad_morg_key,
                pos_mcashier_key,
                ad_muser_key,
                billno,
                seqno,
                sku,
                qty,
                price,
                discount,
                discountname,
                memberid,
                membername,
                isbirthday,
                memberpoint,
                membercardno,
                membertext,
                status,
                ispromomurah
            )
            VALUES
            (
                get_uuid(),
                rec.isactived,
                now(),
                rec.insertby,
                rec.postby,
                now(),
                rec.ad_mclient_key,
                rec.ad_morg_key,
                rec.pos_mcashier_key,
                rec.ad_muser_key,
                rec.billno,

                (
                    SELECT COALESCE(MAX(seqno),0)+1
                    FROM pos_dtempsalesline
                    WHERE billno = p_billno
                ),

                rec.sku,
                rec.max_kelipatan,
                rec.price,

                rec.pricediscount,
                rec.discountname,

                rec.memberid,
                rec.membername,
                rec.isbirthday,
                rec.memberpoint,
                rec.membercardno,
                rec.membertext,
                rec.status,

                true
            );

            ------------------------------------------------------------------
            -- KURANGI BARIS ASLI
            ------------------------------------------------------------------
            UPDATE pos_dtempsalesline
            SET qty = qty - rec.max_kelipatan
            WHERE pos_dtempsalesline_key = rec.pos_dtempsalesline_key;

        ELSE

            ------------------------------------------------------------------
            -- LANGSUNG DISKON
            ------------------------------------------------------------------
            UPDATE pos_dtempsalesline
            SET
                discount      = rec.pricediscount,
                discountname  = rec.discountname,
                ispromomurah  = true
            WHERE pos_dtempsalesline_key = rec.pos_dtempsalesline_key;

        END IF;

    END IF;

END LOOP;



	
select into v_total sum(qty*(price-discount)) from pos_dtempsalesline where billno like p_billno and status like 'WAITING' and pos_mcashier_key=p_pos_mcashier_key;
 v_strtotal='Rp. '||to_char(v_total, 'FM999,999,999');

execute 'select array_to_json(array_agg(row_to_json(t)))
      from (
  		Select $1 as lasttempvalue,$2 as lasttempstring,$3 as isbuygetshow
  		) t '
      INTO o_data
      using v_total,v_strtotal,coalesce(v_isbuyget,false);
 
END;
$function$
;
