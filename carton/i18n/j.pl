#!/usr/bin/perl



my @f = `find ./*`;
@f = map{ $_ =~ s/\n//; $_ } sort( @f );


foreach my $old (@f) {
  next if $old =~ m/j\.pl/;

  if( -f $old ) {
    print $old . "\n";
    my $content = '';

    open( IN, $old );

    while(<IN>) {
      $_ =~ s/(woocommerce|woothemes|woo|wc_)/rpl($1)/egmi;
      $content .= $_;
    }
    close(IN);

    `rm $old`;
    $old =~ s/(woocommerce|woo|wc_)/rpl($1)/egmi;

    open(OUT,'> '. $old);
    print OUT $content;
    close OUT;

  }
}
foreach my $old (@f) {
  if( -d $old ) {
    my $new = $old;
    $new =~ s/(woocommerce|woo|wc_)/rpl($1)/egmi;

    `mv $old $new` if( $new ne $old );
  }
}


exit;


sub rpl {
  my $str = shift;

  if( $str eq 'woocommerce' ) { $str = 'carton' }
  elsif( $str eq 'Woocommerce' ) { $str = 'Carton' }
  elsif( $str eq 'WooCommerce' ) { $str = 'CartoN' }

  elsif( $str eq 'Woo' ) { $str = 'Cart' }
  elsif( $str eq 'WOO' ) { $str = 'CART' }
  elsif( $str eq 'WOOCOMMERCE' ) { $str = 'CARTON' }

  elsif( $str eq 'WC_' ) { $str = 'CTN_' }
  elsif( $str eq 'wc_' ) { $str = 'ctn_' }

  elsif( $str =~ m/WooThemes/ ) { $str = 'CartonThemes' }
  elsif( $str =~ m/woothemes/ ) { $str = 'carton-ecommerce' }

  return $str;
}
