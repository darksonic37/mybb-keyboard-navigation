function keydown(e)
{
	if(e.keyCode == Event.KEY_LEFT)
	{
		if(prevpage == page)
		{
			return;
		}
		else
		{
			window.location = prevpage;
		}
	}

	if(e.keyCode == Event.KEY_RIGHT)
	{
		if(nextpage == page)
		{
			return;
		}
		else
		{
			window.location = nextpage;
		}
	}

	if(e.keyCode == 78)
	{
		if(nexturl == url)
		{
			return;
		}
		else
		{
			window.location = nexturl;
		}
	}

	if(e.keyCode == 80)
	{
		if(prevurl == url)
		{
			return;
		}
		else
		{
			window.location = prevurl;
		}
	}

	if(e.keyCode == 66)
	{
		window.location = random;
	}
}

Event.observe(document, 'keydown', keydown);
