create table customers (
	customer_id int unsigned not null auto_increment primary key,
	name char(60) not null,
  email char(100) not null,
  phone char(100) not null,
	city char(30) not null,
	state char(60)
) ENGINE=InnoDB;

create table orders  (
	order_id int unsigned not null auto_increment primary key,
	customer_id int unsigned not null references customers(customer_id),
	amount float(6,2),
  date date not null,
  order_status char(10),
  ship_name char(60) not null,
  ship_city char(30) not null,
  ship_state char(20)
) ENGINE=InnoDB;

create table books  (
  isbn char(13) not null primary key,
  author char(100),
  title char(100),
  cat_id int unsigned,
  price float(4,2) not null,
  img_src varchar(300),
  shortdescription text,
  description text
) ENGINE=InnoDB;

create table categories  (
  cat_id int unsigned not null auto_increment primary key,
  catname char(60) not null
) ENGINE=InnoDB;

create table order_items  (
  order_id int unsigned not null references orders(order_id),
  isbn char(13) not null references books(isbn),
  item_price float(4,2) not null,
  quantity tinyint unsigned not null,
  primary key (order_id, isbn)
) ENGINE=InnoDB;

create table admin  (
  username char(16) not null primary key,
  passwd char(40) not null
);

